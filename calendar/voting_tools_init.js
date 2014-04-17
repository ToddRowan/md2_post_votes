jQuery(document).ready( function() {
    jQuery('#newVoteRange .date').datepicker({
            'format': 'm/d/yyyy',
            'autoclose': true
        });

    // initialize datepair
    jQuery('#newVoteRange').datepair({defaultDateDelta:null});               
});
