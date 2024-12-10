<?php


use Phinx\Migration\AbstractMigration;

class JasmpInit extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {

      // users table
      $this->execute("
        CREATE TABLE users(
          id VARCHAR(36) NOT NULL,	
          u_name VARCHAR(30) NOT NULL,
          email TEXT NOT NULL,
          password VARCHAR(64) NOT NULL,
          profile_picture VARCHAR(512) NULL,
          bio VARCHAR(512) NULL,
          role VARCHAR(10) NOT NULL,
          created_by VARCHAR(36) NOT NULL,	
          created_date DATETIME DEFAULT NOW(),
          updated_by VARCHAR(36) NULL,
          updated_date DATETIME NULL,
          CONSTRAINT users_pk PRIMARY KEY (id),	
          CONSTRAINT users_unique UNIQUE KEY (u_name)
        );
      ");

      //posts table
      $this->execute("
        CREATE TABLE posts(
          id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36) NOT NULL,
          post VARCHAR(512) NOT NULL,
          caption VARCHAR(1280) NULL,
          time_stamp DATETIME DEFAULT NOW(),
          created_by VARCHAR(36) NOT NULL,	
          created_date DATETIME DEFAULT NOW(),
          updated_by VARCHAR(36) NULL,
          updated_date DATETIME NULL,
          CONSTRAINT post_pk PRIMARY KEY (id),
          CONSTRAINT user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
      ");

      //updates table
      $this->execute("
        CREATE TABLE comments(
          id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36) NOT NULL,
          post_id VARCHAR(36) NOT NULL,
          comment VARCHAR(1024) NOT NULL,
          parent_comment_id VARCHAR(36) DEFAULT NULL,
          created_by VARCHAR(36) NOT NULL,	
          created_date DATETIME DEFAULT NOW(),
          updated_by VARCHAR(36) NULL,
          updated_date DATETIME NULL,
          CONSTRAINT comment_pk PRIMARY KEY (id),
          CONSTRAINT commenter_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
          CONSTRAINT post_fk FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
          CONSTRAINT reply_fk FOREIGN KEY (parent_comment_id) REFERENCES comments(id) ON DELETE CASCADE
        );
      ");


      //likes table
      $this->execute("
        CREATE TABLE likes(
          id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36) NOT NULL,
          post_id VARCHAR(36) NOT NULL,
          created_by VARCHAR(36) NOT NULL,	
          created_date DATETIME DEFAULT NOW(),
          updated_by VARCHAR(36) NULL,
          updated_date DATETIME NULL,
          CONSTRAINT like_pk PRIMARY KEY (id),
          CONSTRAINT user_like_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
          CONSTRAINT post_like_fk FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        );
      ");
    }
}
