<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200518083855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE embed_media DROP FOREIGN KEY FK_AAAC876E5AAD6AB1');
        $this->addSql('DROP INDEX IDX_AAAC876E5AAD6AB1 ON embed_media');
        $this->addSql('ALTER TABLE embed_media CHANGE trick_embed_media_id trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE embed_media ADD CONSTRAINT FK_AAAC876EB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_AAAC876EB281BE2E ON embed_media (trick_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE embed_media DROP FOREIGN KEY FK_AAAC876EB281BE2E');
        $this->addSql('DROP INDEX IDX_AAAC876EB281BE2E ON embed_media');
        $this->addSql('ALTER TABLE embed_media CHANGE trick_id trick_embed_media_id INT NOT NULL');
        $this->addSql('ALTER TABLE embed_media ADD CONSTRAINT FK_AAAC876E5AAD6AB1 FOREIGN KEY (trick_embed_media_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_AAAC876E5AAD6AB1 ON embed_media (trick_embed_media_id)');
    }
}
