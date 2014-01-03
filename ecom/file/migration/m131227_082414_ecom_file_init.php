<?php
/**
 * Migration for ecom-file component.
 *
 * @author Jin Hu <bixuehujin@gmail.com>
 */
class m131227_082414_ecom_file_init extends CDbMigration
{
    public function up()
    {
        $this->createTable('file_managed', [
            'fid'       => "int(11)      unsigned NOT NULL AUTO_INCREMENT",
            'uid'       => "int(11)      unsigned NOT NULL DEFAULT 0",
            'domain'    => "varchar(50)           NOT NULL DEFAULT ''",
            'hash'      => "binary(27)            NOT NULL DEFAULT ''",
            'name'      => "varchar(255)          NOT NULL DEFAULT ''",
            'mime'      => "varchar(100)          NOT NULL DEFAULT ''",
            'size'      => "int(11)      unsigned NOT NULL DEFAULT 0",
            'status'    => "tinyint               NOT NULL DEFAULT 0",
            'created'   => "timestamp             NOT NULL DEFAULT CURRENT_TIMESTAMP",
            'PRIMARY KEY `fid` (`fid`)',
            'KEY `uid` (`uid`)',
            'UNIQUE KEY `hash`(`hash`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->createTable('file_usage', [
            'fid'         => "int(11)     unsigned NOT NULL DEFAULT 0",
            'entity_type' => "varchar(20)          NOT NULL DEFAULT ''",
            'entity_id'   => "int(11)     unsigned NOT NULL DEFAULT 0",
            'type'        => "tinyint     unsigned NOT NULL DEFAULT 0",
            'count'       => "int(11)     unsigned NOT NULL DEFAULT 0",
            'PRIMARY KEY (`fid`, `entity_type`, `entity_id`, `type`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }

    public function down()
    {
        $this->dropTable('file_managed');
        $this->dropTable('file_usage');

        return true;
    }
}
