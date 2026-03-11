<?php
namespace Drupal\tripal_db_sequence_checker\Commands;

use Drush\Commands\DrushCommands;

/**
 * Drush commands.
 */
class SequenceChecker extends DrushCommands {
  
  /**
   * @command sequence_checker
   */
  public function sequenceChecker() {
    // Check ALL the tables!
    $this->getSequenceInfo();
  }

  /**
   * @command sequence_checker:table
   * canary
   */
  public function sequenceCheckerTable(String $table = '') {
    // If $table is not provided, assume check all tables.
  }

  /**
   * @command sequence_checker:sequence
   * canary
   */
  public function sequenceCheckerSequence(String $sequence = '') {
    // If $sequence is not provided, assume check all tables.
  }

  /**
   * Helper function to actually do the work.
   */
  public function getSequenceInfo() {

    $database = \Drupal::database();

    $query = "SELECT
    ns.nspname AS schema_name,
    t.relname AS table_name,
    a.attname AS column_name,
    s.relname AS sequence_name,
    q.last_value as last_value
      FROM pg_class s
      JOIN pg_namespace ns ON ns.oid = s.relnamespace
      JOIN pg_depend d ON d.objid = s.oid
      JOIN pg_class t ON d.refobjid = t.oid
      JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = d.refobjsubid
      JOIN pg_sequences q on q.sequencename = s.relname
      WHERE s.relkind = 'S' and q.last_value is not null
      ORDER BY q.last_value desc;
    ";

    $results = $database->query($query)->fetchAll();

    // Assemble a nicer array with keys being 'table_name.column_name' with 
    // inner values of sequence_name, last_value.
    $table_details = [];
    foreach ($results as $result) {
      $table = $result->table_name;
      $schema = $result->schema_name;
      $sequence_column = $result->column_name;
      $sequence_name = $result->sequence_name;
      $last_value = $result->last_value;
      
      // Get max values.
      $max_query = "SELECT max(" . $sequence_column . ") FROM " . $schema . "." . $table;
      $max_value = $database->query($max_query)->fetchField();

      $table_details[$schema . '.' . $table] = [
        'sequence_column' => $sequence_column,
        'sequence_name' => $sequence_name,
        'last_value' => $last_value,
        'max_value' => $max_value,
      ];
    }

    // Loop through the table_details and find any tables whose sequences'
    // last_value is less than its max_value.

    foreach ($table_details as $table) {
      if ( isset($table->last_value) && isset($table->max_value) ) {
        if ($table->last_value < $table->max_value) {
          print $table_name . " is bad! OOh!\n";
        }
      }
    }
    // print_r($table_details);
  }
}