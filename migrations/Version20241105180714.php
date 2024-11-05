<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241105180714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the changelog table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EXTENSION multicorn');
        $this->addSql(<<<'SQL'
                CREATE SERVER rabbitmq FOREIGN DATA WRAPPER multicorn
                OPTIONS (
                    wrapper 'rabbitmq_fdw.RabbitmqFDW',
                    host 'rabbitmq',
                    virtual_host '/',
                    port '5672',
                    exchange ''
                )
            SQL);
        $this->addSql(<<<'SQL'
                CREATE FOREIGN TABLE changelog (
                    body text
                )
                SERVER rabbitmq
                OPTIONS (
                    bulk_size '10',
                    queue 'changelog',
                    column 'body'
                )
            SQL);
        $this->addSql(<<<'SQL'
                create or replace function changelog_trigger() returns trigger as $$
                declare
                    action text;
                    table_name text;
                    transaction_id bigint;
                    timestamp timestamp;
                    old_data jsonb;
                    new_data jsonb;
                begin
                    action := lower(TG_OP::text);
                    table_name := TG_TABLE_NAME::text;
                    transaction_id := txid_current();
                    timestamp := current_timestamp;

                    if TG_OP = 'DELETE' then
                        old_data := to_jsonb(OLD.*);
                    elseif TG_OP = 'INSERT' then
                        new_data := to_jsonb(NEW.*);
                    elseif TG_OP = 'UPDATE' then
                        old_data := to_jsonb(OLD.*);
                        new_data := to_jsonb(NEW.*);
                    end if;

                insert into changelog (body)
                values (json_build_object('action', action, 'table_name', table_name, 'transaction_id', transaction_id, 'timestamp', timestamp, 'old_data', old_data, 'new_data', new_data)::text);

                return null;
                end;
                $$ language plpgsql
            SQL);
        $this->addSql(<<<'SQL'
                create trigger article_changelog_trigger
                    after insert or update or delete on article
                    for each row execute function changelog_trigger()
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
