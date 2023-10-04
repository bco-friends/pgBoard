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
}
