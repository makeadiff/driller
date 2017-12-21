# Driller

This is a multi-purpose reporting tool for drill-down kind of data. 

## How it works

To create a new data-type that you want a drill down for, create a new file in the 'data_type' folder. You may need to create a model for it in the 'models' folder if you want to keep the code base clean. The date-type PHP file should have...

* $page_title = 'Title'
* $structure = a multi-dimensional array to show the structure tree. 
* getCollectiveData() // Will return aggregate data at every level - City, Center, Batch, Vertical, etc.
* getIndividualData() // Will return person data at the final level - for eg. returns data of all users in a batch.

To get the formats for these things, check one of the existing components.

## Components

### Impact Survey

File : data_types/impact_survey.php

This shows the adoption of the Impact Survey within ESMA. 

### CCP Agreements

File : data_types/cpp_agreements.php

Shows all who have agreed to the Child Protection Policy

### CFR Participation

File : data_types/cfr_participation.php

Shows CFR participation for each level.

## Owner

* Binny V A
