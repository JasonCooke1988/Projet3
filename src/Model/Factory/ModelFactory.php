<?php
namespace App\Model\Factory;
use App\Model\PDOModel;
/**
 * ModelFactory
 * Creates the Model if it doesn't exist
 */
class ModelFactory
{

    /**
     * Models array
     */
    private static $models = [];

    /**
     * Returns the Model if it exists or creates it before returning it
     */
    public static function getModel(string $table)
    {
        if (array_key_exists($table, self::$models)) {
            return self::$models[$table];
        }
        $class = 'App\Model\\' . ucfirst($table) . 'Model';
        self::$models[$table] = new $class(new PDOModel(PDOFactory::getPDO()));
        return self::$models[$table];
    }
}