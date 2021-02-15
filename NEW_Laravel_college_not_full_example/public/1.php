<style>

.tablesorter .filtered {
    display: none;
}

/* ajax error row */
.tablesorter .tablesorter-errorRow td {
    text-align: center;
    cursor: pointer;
    background-color: #e6bf99;
}

</style>



Search <button type="button" data-filter-column="5" data-filter-text="2?%">2?%</button> in the Discount column<br>
<button type="button" class="reset">Reset</button> <!-- targeted by the "filter_reset" option -->
<button type="button" class="resetsaved" title="Reload the page to see that the saved filters have cleared">Reset Saved Filters</button>

<table id="table" class="tablesorter">
  <thead>
    <tr>
      <!-- you can also add a placeholder using script; $('.tablesorter th:eq(0)').data('placeholder', 'hello') -->
      <th data-placeholder="" class="filter-false">Rank</th>
      <th data-placeholder="Try B*{space} or alex|br*|c" data-label="First Name" data-test="" class="filter-match">First Name (<span></span> filter-match )</th>
      <th data-placeholder="Try <d">Last Name</th>
      <th data-placeholder="Try >=33">Age</th>
      <th data-placeholder="Try <9.99">Total</th>
      <th data-placeholder="Try 2?%">Discount</th> <!-- add class="filter-false" to disable the filter in this column -->
      <th data-placeholder="Try /20[^0]\d/ or >1/1/2010">Date</th>
    </tr>
  </thead>
  <tbody>
    <tr><td>1</td><td>Philip Aaron Wong</td><td>Johnson Sr Esq</td><td>25</td><td>$5.95</td><td>22%</td><td>Jun 26, 2004 7:22 AM</td></tr>
    <tr><td>11</td><td>Aaron</td><td>Hibert</td><td>12</td><td>$2.99</td><td>5%</td><td>Aug 21, 2009 12:21 PM</td></tr>
    <tr><td>12</td><td>Brandon Clark</td><td>Henry Jr</td><td>51</td><td>$42.29</td><td>18%</td><td>Oct 13, 2000 1:15 PM</td></tr>
    <tr><td>111</td><td>Peter</td><td>Parker</td><td>28</td><td>$9.99</td><td>20%</td><td>Jul 6, 2006 8:14 AM</td></tr>
    <tr><td>21</td><td>John</td><td>Hood</td><td>33</td><td>$19.99</td><td>25%</td><td>Dec 10, 2002 5:14 AM</td></tr>
    <tr><td>013</td><td>Clark</td><td>Kent Sr.</td><td>18</td><td>$15.89</td><td>44%</td><td>Jan 12, 2003 11:14 AM</td></tr>
    <tr><td>005</td><td>Bruce</td><td>Almighty Esq</td><td>45</td><td>$153.19</td><td>44%</td><td>Jan 18, 2021 9:12 AM</td></tr>
    <tr><td>10</td><td>Alex</td><td>Dumass</td><td>13</td><td>$5.29</td><td>4%</td><td>Jan 8, 2012 5:11 PM</td></tr>
    <tr><td>16</td><td>Jim</td><td>Franco</td><td>24</td><td>$14.19</td><td>14%</td><td>Jan 14, 2004 11:23 AM</td></tr>
    <tr><td>166</td><td>Bruce Lee</td><td>Evans</td><td>22</td><td>$13.19</td><td>11%</td><td>Jan 18, 2007 9:12 AM</td></tr>
    <tr><td>100</td><td>Brenda Dexter</td><td>McMasters</td><td>18</td><td>$55.20</td><td>15%</td><td>Feb 12, 2010 7:23 PM</td></tr>
    <tr><td>55</td><td>Dennis</td><td>Bronson</td><td>65</td><td>$123.00</td><td>32%</td><td>Jan 20, 2001 1:12 PM</td></tr>
    <tr><td>9</td><td>Martha</td><td>delFuego</td><td>25</td><td>$22.09</td><td>17%</td><td>Jun 11, 2011 10:55 AM</td></tr>
  </tbody>
</table>

<script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
  
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/extras/jquery.tablesorter.pager.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>


<script>

$(function() {

  // call the tablesorter plugin
  $("#table").tablesorter({
    theme: 'blue',
  debug: true,
    // hidden filter input/selects will resize the columns, so try to minimize the change
    widthFixed : true,

    // initialize zebra striping and filter widgets
    widgets: ["zebra", "filter"],

    ignoreCase: false,

    widgetOptions : {

      // filter_anyMatch options was removed in v2.15; it has been replaced by the filter_external option

      // If there are child rows in the table (rows with class name from "cssChildRow" option)
      // and this option is true and a match is found anywhere in the child row, then it will make that row
      // visible; default is false
      filter_childRows : false,

      // if true, filter child row content by column; filter_childRows must also be true
      filter_childByColumn : false,

      // if true, include matching child row siblings
      filter_childWithSibs : true,

      // if true, a filter will be added to the top of each table column;
      // disabled by using -> headers: { 1: { filter: false } } OR add class="filter-false"
      // if you set this to false, make sure you perform a search using the second method below
      filter_columnFilters : true,

      // if true, allows using "#:{query}" in AnyMatch searches (column:query; added v2.20.0)
      filter_columnAnyMatch: true,

      // extra css class name (string or array) added to the filter element (input or select)
      filter_cellFilter : '',

      // extra css class name(s) applied to the table row containing the filters & the inputs within that row
      // this option can either be a string (class applied to all filters) or an array (class applied to indexed filter)
      filter_cssFilter : '', // or []

      // add a default column filter type "~{query}" to make fuzzy searches default;
      // "{q1} AND {q2}" to make all searches use a logical AND.
      filter_defaultFilter : {},

      // filters to exclude, per column
      filter_excludeFilter : {},

      // jQuery selector (or object) pointing to an input to be used to match the contents of any column
      // please refer to the filter-any-match demo for limitations - new in v2.15
      filter_external : '',

      // class added to filtered rows (rows that are not showing); needed by pager plugin
      filter_filteredRow : 'filtered',

      // ARIA-label added to filter input/select; {{label}} is replaced by the column header
      // "data-label" attribute, if it exists, or it uses the column header text
      filter_filterLabel : 'Filter "{{label}}" column by...',

      // add custom filter elements to the filter row
      // see the filter formatter demos for more specifics
      filter_formatter : null,

      // add custom filter functions using this option
      // see the filter widget custom demo for more specifics on how to use this option
      filter_functions : null,

      // hide filter row when table is empty
      filter_hideEmpty : true,

      // if true, filters are collapsed initially, but can be revealed by hovering over the grey bar immediately
      // below the header row. Additionally, tabbing through the document will open the filter row when an input gets focus
      // in v2.26.6, this option will also accept a function
      filter_hideFilters : true,

      // Set this option to false to make the searches case sensitive
      filter_ignoreCase : true,

      // if true, search column content while the user types (with a delay).
      // In v2.27.3, this option can contain an
      // object with column indexes or classnames; "fallback" is used
      // for undefined columns
      filter_liveSearch : true,

      // global query settings ('exact' or 'match'); overridden by "filter-match" or "filter-exact" class
      filter_matchType : { 'input': 'exact', 'select': 'exact' },

      // a header with a select dropdown & this class name will only show available (visible) options within that drop down.
      filter_onlyAvail : 'filter-onlyAvail',

      // default placeholder text (overridden by any header "data-placeholder" setting)
      filter_placeholder : { search : '', select : '' },

      // jQuery selector string of an element used to reset the filters
      filter_reset : 'button.reset',

      // Reset filter input when the user presses escape - normalized across browsers
      filter_resetOnEsc : true,

      // Use the $.tablesorter.storage utility to save the most recent filters (default setting is false)
      filter_saveFilters : true,

      // Delay in milliseconds before the filter widget starts searching; This option prevents searching for
      // every character while typing and should make searching large tables faster.
      filter_searchDelay : 300,

      // allow searching through already filtered rows in special circumstances; will speed up searching in large tables if true
      filter_searchFiltered: true,

      // include a function to return an array of values to be added to the column filter select
      filter_selectSource  : null,

      // if true, server-side filtering should be performed because client-side filtering will be disabled, but
      // the ui and events will still be used.
      filter_serversideFiltering : false,

      // Set this option to true to use the filter to find text from the start of the column
      // So typing in "a" will find "albert" but not "frank", both have a's; default is false
      filter_startsWith : false,

      // Filter using parsed content for ALL columns
      // be careful on using this on date columns as the date is parsed and stored as time in seconds
      filter_useParsedData : false,

      // data attribute in the header cell that contains the default filter value
      filter_defaultAttrib : 'data-value',

      // filter_selectSource array text left of the separator is added to the option value, right into the option text
      filter_selectSourceSeparator : '|'

    }

  });

  // Clear stored filters - added v2.25.6
  $('.resetsaved').click(function() {
    $('#table').trigger('filterResetSaved');

    // show quick popup to indicate something happened
    var $message = $('<span class="results"> Reset</span>').insertAfter(this);
    setTimeout(function() {
      $message.remove();
    }, 500);
    return false;
  });

  // External search
  // buttons set up like this:
  // <button type="button" data-filter-column="4" data-filter-text="2?%">Saved Search</button>
  $('button[data-filter-column]').click(function() {
    /*** first method *** data-filter-column="1" data-filter-text="!son"
      add search value to Discount column (zero based index) input */
    var filters = [],
      $t = $(this),
      col = $t.data('filter-column'), // zero-based index
      txt = $t.data('filter-text') || $t.text(); // text to add to filter

    filters[col] = txt;
    // using "table.hasFilters" here to make sure we aren't targeting a sticky header
    $.tablesorter.setFilters( $('#table'), filters, true ); // new v2.9

    /** old method (prior to tablsorter v2.9 ***
    var filters = $('table.tablesorter').find('input.tablesorter-filter');
    filters.val(''); // clear all filters
    filters.eq(col).val(txt).trigger('search', false);
    ******/

    /*** second method ***
      this method bypasses the filter inputs, so the "filter_columnFilters"
      option can be set to false (no column filters showing)
    ******/
    /*
    var columns = [];
    columns[5] = '2?%'; // or define the array this way [ '', '', '', '', '', '2?%' ]
    $('table').trigger('search', [ columns ]);
    */

    return false;
  });
  
  
  var filters = $.tablesorter.getFilters($tableDom);
helper.customFilter(component, filters[1], 1)
  

});


</script>