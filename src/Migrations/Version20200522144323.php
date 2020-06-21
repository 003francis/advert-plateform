<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200522144323 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE advert_rappel_category (advert_rappel_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_A3F0E6DE0483F9A (advert_rappel_id), INDEX IDX_A3F0E6D12469DE2 (category_id), PRIMARY KEY(advert_rappel_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE advert_rappel_category ADD CONSTRAINT FK_A3F0E6DE0483F9A FOREIGN KEY (advert_rappel_id) REFERENCES advert_rappel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE advert_rappel_category ADD CONSTRAINT FK_A3F0E6D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE advert_rappel_category DROP FOREIGN KEY FK_A3F0E6D12469DE2');
        $this->addSql('DROP TABLE advert_rappel_category');
        $this->addSql('DROP TABLE category');
    }
}
