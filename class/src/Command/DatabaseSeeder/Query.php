<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command\DatabaseSeeder;

use DB;

class Query
{
  public function __construct(
    private readonly DB $db
  ) {}

  public function createRandomizationQuery(): void
  {
    $indexQuery = <<<SQL
        CREATE OR REPLACE FUNCTION random_between(low integer, high integer)
               RETURNS integer
               LANGUAGE plpgsql
               STRICT
               AS \$function\$
               BEGIN
                RETURN floor(random()* (high-low +1) + low);
               END;
              \$function\$;
    SQL;

    $result = $this->db->query($indexQuery);

    if (is_bool($result)) {
      throw new \Exception("Failed to create random_between function.");
    }
  }

  /**
   * Get a random user from the database.
   *
   * @return array
   */
  public function getRandomMember(): array
  {
    $result = pg_fetch_all(
      $this->db->query('SELECT * FROM member WHERE id IN (SELECT random_between(min(id), max(id)) FROM member LIMIT 1)')
    );

    return !empty($result) ? array_pop($result) : [];
  }

  public function getRandomMemberId(): int
  {
    return (int)pg_fetch_result(
      $this->db->query("SELECT random_between(min(id), max(id)) from member LIMIT 1"),
      0,
      0
    );
  }
}
