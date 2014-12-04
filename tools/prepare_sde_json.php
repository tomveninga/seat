<?php

// Prepare the data that we want represented as JSON
$d = [
   'version' => 'phoebe-1.0-107269',
   'url' => 'https://www.fuzzwork.co.uk/dump/phoebe-1.0-107269/',
   'format' => '.sql.bz2',
   'tables' => [
      'dgmTypeAttributes',
      'invBlueprintTypes',
      'invCategories',
      'invContrabandTypes',
      'invControlTowerResourcePurposes',
      'invControlTowerResources',
      'invFlags',
      'invGroups',
      'invItems',
      'invMarketGroups',
      'invMetaGroups',
      'invMetaTypes',
      'invNames',
      'invPositions',
      'invTypeMaterials',
      'invTypeReactions',
      'invTypes',
      'invUniqueNames',
      'mapDenormalize',
      'staStations'
   ]
];

// Print the resultant JSON
print_r(json_encode($d) . PHP_EOL);

