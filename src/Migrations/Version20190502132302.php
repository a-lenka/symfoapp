<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190502132302
 * @package DoctrineMigrations
 */
final class Version20190502132302 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add `owner` field to Task Entity';
    }


    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // This up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('
            ALTER TABLE task 
                ADD owner_id INT NOT NULL
        ');

        $this->addSql('
            ALTER TABLE task 
                ADD CONSTRAINT FK_527EDB257E3C61F9 
                    FOREIGN KEY (owner_id) 
                    REFERENCES user (id)
        ');

        $this->addSql('
            CREATE INDEX IDX_527EDB257E3C61F9 
                ON task (owner_id)
        ');
    }


    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // This down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('
            ALTER TABLE task 
                DROP FOREIGN KEY FK_527EDB257E3C61F9
        ');

        $this->addSql('
            DROP INDEX IDX_527EDB257E3C61F9 
                ON task
        ');

        $this->addSql('
            ALTER TABLE task 
                DROP owner_id
        ');
    }
}
