# PHP-EntityManager
Database ORM based on Java's Entity Manager pattern. You can use PHP EntityManager generically by
using the default Entity class or define your own subclasses of the Entity class.

The entity class makes the magic happen by using magic getters and setters to read and modify its internal data. This allows you to use the entity class as-is. 

# Find
$criteria = array("id" => 10);
$entityManager->findBy("table_name", $criteria, 0, 100);

# Find All
$entities = $entityManager->findAll("table_name");

# Create
$entity = $entityManager->createManagedEntity("table_name");
$entity->first = "John";
$entity->last = "Doe";
$entity->create();

# Update
$entity->update();

# Delete
$entity->remove();

# Reload from database
$entity->load();

# Init 
$person = array("first" => "John", "last" => "Doe");
$entity = $entityManager->createManagedEntity("table_name");
$entity->init($person);
$entity->create();


