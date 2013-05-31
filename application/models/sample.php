<?php
class Model_Sample {
    function sampleMethod() {
        // Access the database model to get data from database
        // Or get some other data, from a $_SESSION for example.
        // And return that.
        // BUT NO FRICKIN' HTML here. EVER!
        return 'This is some sample data from the sample model';
    }
}