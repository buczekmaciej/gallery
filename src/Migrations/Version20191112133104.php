<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191112133104 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_B78FCCA08D7B4FB4');
        $this->addSql('DROP INDEX IDX_B78FCCA0A21214B7');
        $this->addSql('CREATE TEMPORARY TABLE __temp__categories_tags AS SELECT categories_id, tags_id FROM categories_tags');
        $this->addSql('DROP TABLE categories_tags');
        $this->addSql('CREATE TABLE categories_tags (categories_id INTEGER NOT NULL, tags_id INTEGER NOT NULL, PRIMARY KEY(categories_id, tags_id), CONSTRAINT FK_B78FCCA0A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B78FCCA08D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO categories_tags (categories_id, tags_id) SELECT categories_id, tags_id FROM __temp__categories_tags');
        $this->addSql('DROP TABLE __temp__categories_tags');
        $this->addSql('CREATE INDEX IDX_B78FCCA08D7B4FB4 ON categories_tags (tags_id)');
        $this->addSql('CREATE INDEX IDX_B78FCCA0A21214B7 ON categories_tags (categories_id)');
        $this->addSql('DROP INDEX IDX_472B783A12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gallery AS SELECT id, category_id, image, likes, views, saves FROM gallery');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('CREATE TABLE gallery (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, image BLOB NOT NULL, likes INTEGER NOT NULL, views INTEGER NOT NULL, saves INTEGER NOT NULL, CONSTRAINT FK_472B783A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO gallery (id, category_id, image, likes, views, saves) SELECT id, category_id, image, likes, views, saves FROM __temp__gallery');
        $this->addSql('DROP TABLE __temp__gallery');
        $this->addSql('CREATE INDEX IDX_472B783A12469DE2 ON gallery (category_id)');
        $this->addSql('ALTER TABLE user ADD COLUMN color_scheme VARCHAR(5) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_1F2666304E7AF8F');
        $this->addSql('DROP INDEX IDX_1F266630A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_gallery AS SELECT user_id, gallery_id FROM user_gallery');
        $this->addSql('DROP TABLE user_gallery');
        $this->addSql('CREATE TABLE user_gallery (user_id INTEGER NOT NULL, gallery_id INTEGER NOT NULL, PRIMARY KEY(user_id, gallery_id), CONSTRAINT FK_1F266630A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1F2666304E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_gallery (user_id, gallery_id) SELECT user_id, gallery_id FROM __temp__user_gallery');
        $this->addSql('DROP TABLE __temp__user_gallery');
        $this->addSql('CREATE INDEX IDX_1F2666304E7AF8F ON user_gallery (gallery_id)');
        $this->addSql('CREATE INDEX IDX_1F266630A76ED395 ON user_gallery (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_B78FCCA0A21214B7');
        $this->addSql('DROP INDEX IDX_B78FCCA08D7B4FB4');
        $this->addSql('CREATE TEMPORARY TABLE __temp__categories_tags AS SELECT categories_id, tags_id FROM categories_tags');
        $this->addSql('DROP TABLE categories_tags');
        $this->addSql('CREATE TABLE categories_tags (categories_id INTEGER NOT NULL, tags_id INTEGER NOT NULL, PRIMARY KEY(categories_id, tags_id))');
        $this->addSql('INSERT INTO categories_tags (categories_id, tags_id) SELECT categories_id, tags_id FROM __temp__categories_tags');
        $this->addSql('DROP TABLE __temp__categories_tags');
        $this->addSql('CREATE INDEX IDX_B78FCCA0A21214B7 ON categories_tags (categories_id)');
        $this->addSql('CREATE INDEX IDX_B78FCCA08D7B4FB4 ON categories_tags (tags_id)');
        $this->addSql('DROP INDEX IDX_472B783A12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gallery AS SELECT id, category_id, image, likes, views, saves FROM gallery');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('CREATE TABLE gallery (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, image BLOB NOT NULL, likes INTEGER NOT NULL, views INTEGER NOT NULL, saves INTEGER NOT NULL)');
        $this->addSql('INSERT INTO gallery (id, category_id, image, likes, views, saves) SELECT id, category_id, image, likes, views, saves FROM __temp__gallery');
        $this->addSql('DROP TABLE __temp__gallery');
        $this->addSql('CREATE INDEX IDX_472B783A12469DE2 ON gallery (category_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, login, password, email, reset_hash FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, reset_hash VARCHAR(100) NOT NULL)');
        $this->addSql('INSERT INTO user (id, login, password, email, reset_hash) SELECT id, login, password, email, reset_hash FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('DROP INDEX IDX_1F266630A76ED395');
        $this->addSql('DROP INDEX IDX_1F2666304E7AF8F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_gallery AS SELECT user_id, gallery_id FROM user_gallery');
        $this->addSql('DROP TABLE user_gallery');
        $this->addSql('CREATE TABLE user_gallery (user_id INTEGER NOT NULL, gallery_id INTEGER NOT NULL, PRIMARY KEY(user_id, gallery_id))');
        $this->addSql('INSERT INTO user_gallery (user_id, gallery_id) SELECT user_id, gallery_id FROM __temp__user_gallery');
        $this->addSql('DROP TABLE __temp__user_gallery');
        $this->addSql('CREATE INDEX IDX_1F266630A76ED395 ON user_gallery (user_id)');
        $this->addSql('CREATE INDEX IDX_1F2666304E7AF8F ON user_gallery (gallery_id)');
    }
}
