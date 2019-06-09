<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Class Version20190410172012
 * @package DoctrineMigrations
 */
final class Version20190410172012 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Generated User Entity';
    }


    /**
     * This `up()` migration is auto-generated,
     * please modify it to your needs
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('
            CREATE TABLE user
            (
                id       INT auto_increment NOT NULL,
                email    VARCHAR(180) NOT NULL,
                roles    JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                UNIQUE INDEX uniq_8d93d649e7927c74 (email),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE utf8mb4_unicode_ci
            engine = innodb 
        ');
    }


    /**
     * This `down()` migration is auto-generated,
     * please modify it to your needs
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('
            DROP TABLE user'
        );
    }
}
