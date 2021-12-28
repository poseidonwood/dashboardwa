<?php
require_once (COMMON_INC_DIR.'class.olahDB.php');

class cMessagingWA extends cOlahDB
{
   private $mDb;
   private $mDatabase;

   public function __construct($readonly=false,$db="whatsapp")
   {
      $this->mDatabase=DB_PREFIX.$db;
      if ($readonly)
         parent::__construct($this->mDatabase, SLAVE_DB_HOSTPORT);
      else
         parent::__construct($this->mDatabase);

      $this->mDb = $this->getdbHandler("mysqli");
   }

   public function getUnsentMessages()
   {
      $query = "SELECT id,nomor,pesan ".
               "FROM {$this->mDatabase}.pesan ".
               "WHERE (jadwal<=NOW()) ".
               "AND (status='MENUNGGU JADWAL')";

      $db_result=$this->QueryDB($query,1);
      if (is_array($db_result))
         return $db_result;
      else
      {
         if ($this->debug) echo "Error in query: {$query} ";
         return -1;
      }
   }

   public function update_message_sent($msg_id,$status)
   {
      $query = "UPDATE pesan ".
               "SET status='{$status}' ".
               "WHERE id='{$msg_id}'";

      $db_result=$this->QueryDB($query);
      if ($db_result=="OK")
         return $db_result;
      else
      {
         if ($this->debug) echo "Error in query: {$query} ";
         return -1;
      }
   }
}
?>
