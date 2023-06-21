<?php

// These are global functions

function dump($o)
{
    printf(
        "<pre
    style='overflow: auto; 
    padding: 20px; 
    background-color: #fbfbfb; 
    border: 2px dashed darkred;'>
<strong>DUMP</strong><br><br>
%s
    </pre>",
        print_r($o, true)
    );
}
