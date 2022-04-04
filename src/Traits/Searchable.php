<?php

namespace Flarone\Searchable\Traits;

use Flarone\Searchable\Classes\ImportGenerator;
use Flarone\Searchable\Models\Search;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

trait Searchable
{
    private $buffer = 10;

    protected $current_index;

    protected $importExclude = ['model', 'model_id', 'field', 'parent_model', 'parent_id'];
    protected $importData = [];
    protected $importGenerator;

    protected function usesSoftDeletes(): bool
    {
        return (bool) in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this));
    }

    // DO NOT REMOVE THIS FUNCTION !!
    protected function search() {}

    public function toSearchableArray() {
        return $this->toArray();
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return true;
    }

    protected function generateSearchIndex()
    {
        $this->importGenerator = new ImportGenerator;
        $this->current_index = Search::pluck('id')->all();

        // exclude models from the searches here
        $toExclude = [Search::class];

        // getting all the model files from the model folder
        $files = File::allFiles(app()->basePath() . '/app/Models');

        // to get all the model classes
        print "Getting files to index\n";
        $results = collect($files)->map(function (SplFileInfo $file) {
            $filename = $file->getRelativePathname();
            $filename = str_replace('/', '\\', $filename);

            // assume model name is equal to file name
            /* making sure it is a php file*/
            if (substr($filename, -4) !== '.php') {
                return null;
            }
            // removing .php
            return substr($filename, 0, -4);

        })->filter(function (?string $classname) use ($toExclude) {
            if ($classname === null) {
                return false;
            }

            // using reflection class to obtain class info dynamically
            $reflection = new \ReflectionClass($this->modelNamespacePrefix() . $classname);

            // making sure the class extended eloquent model
            $isModel = $reflection->isSubclassOf(Model::class);

            // making sure the model implemented the searchable trait
            $searchable = $reflection->hasMethod('search');

            // filter model that has the searchable trait and not in exclude array
            return $isModel && $searchable && !in_array($reflection->getName(), $toExclude, true);
        })->map(function ($classname) {
            // for each class, call the search function
            $model = app($this->modelNamespacePrefix() . $classname);

            // We make use of the SEARCHABLE_FIELDS constant in our model
            // we dont want id in the match, so we filter it out.

            print "Searching in " . get_class($model). " records\n";

            $model::get()->map(function ($modelRecord) use ($classname) {
                self::getSearchData($modelRecord, $classname);
            });
        });

        // Clean up the search-index
        Search::whereIn('id', $this->current_index)->forceDelete();
        print "\nDone.\n";
    }

    function getSearchData($modelRecord, $classname, $parent_model = null, $parent_id = null) {
        $reflection_class = (new \ReflectionClass($modelRecord));
        if($reflection_class->hasMethod('shouldBeSearchable') && $modelRecord->shouldBeSearchable()) {
            $fields = array_filter($modelRecord::SEARCHABLE_FIELDS, fn($field) => $field !== 'id');
            $parent_model = $parent_model ?? $reflection_class->getShortName();
            $parent_id = $parent_id ?? $modelRecord->id;
            foreach($fields as $field) {
                if (!empty($modelRecord->{$field})) {

                    $this->importData[] = [
                        'model' => $classname,
                        'model_id' => $modelRecord->id,
                        'field' => $field,
                        'parent_model' => $parent_model,
                        'parent_id' => $parent_id,
                        'searchcontent' => trim(strip_tags($modelRecord->{$field})),
                    ];

//                    $result = Search::updateOrCreate([
//                        'model' => $classname,
//                        'model_id' => $modelRecord->id,
//                        'field' => $field,
//                        'parent_model' => $parent_model,
//                        'parent_id' => $parent_id
//                    ], [
//                        'searchcontent' => trim(strip_tags($modelRecord->{$field})),
//                    ]);

                    if (count($this->importData) > 100) {
                        print ".";
                        $this->importGenerator->generate('search_index', $this->importData, $this->importExclude);
                        $this->importData = [];
                    }
//                    if (($key = array_search($result->id, $this->current_index)) !== false) {
//                        unset($this->current_index[$key]);
//                    }
                }
            }
            if (count($this->importData) > 0) {
                $this->importGenerator->generate('search_index', $this->importData, $this->importExclude);
                $this->importData = [];
            }

            foreach($modelRecord::SEARCHABLE_RELATIONS as $relation) {
                if (empty($fetched)) return;
                print ".";
                $fetched = $modelRecord->{$relation};
                if ($fetched instanceof Collection) {
                    foreach($fetched as $submodel) {
                        $classname = (new \ReflectionClass($submodel))->getShortName();
                        self::getSearchData($submodel, $classname, $parent_model, $parent_id);
                    }
                } else {
                    $classname = (new \ReflectionClass($fetched))->getShortName();
                    self::getSearchData($fetched, $classname, $parent_model, $parent_id);
                }
            }
        }
    }

    /** A helper function to generate the model namespace
     * @return string
     */
    private function modelNamespacePrefix()
    {
        return app()->getNamespace() . 'Models\\';
    }
}