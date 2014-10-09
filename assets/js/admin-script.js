/*!
 * Admin panel helper for Anyway Feedback
 *
 * @package AFB
 */

/*global google:true*/
/*global AFB:true*/

jQuery(document).ready(function($){


    // Post type switcher
    $('#afb-post-type-switcher').change(function(){
        var endpoint = $(this).attr('data-href'),
            postType = $(this).val(),
            args = [];
        if( postType.length ){
            if( 'post' !== postType ){
                args.push('post_type=' + postType);
            }
            args.push('page=anyway-feedback-static-' + postType);
            endpoint += '?' + args.join('&');
            window.location.href = endpoint;
        }
    });


});

(function($){
    if( AFB ){
        // Load before page finishes loading
        google.load('visualization', '1.0', {'packages':['corechart']});
        // Set a callback to run when the Google Visualization API is loaded.
        google.setOnLoadCallback(function(){
            // Chart
            var chartArea =  $('#chart-area');
            if( chartArea.length ){
                var endpoint = chartArea.attr('data-endpoint');
                $.get(endpoint, function(result){

                    //
                    // Create Pie Chart
                    //
                    if( !result.ratio.negative ){
                        $('#afb-pie-chart').removeClass('loading').append('<p class="no-data">' + AFB.noData + '</p>');
                    }else{
                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Response');
                        data.addColumn('number', 'Count');
                        data.addRows([
                            [AFB.piePositive, parseInt(result.ratio.positive)],
                            [AFB.pieNegative, parseInt(result.ratio.negative)]
                        ]);
                        // Set chart options
                        var options = {
                            title: AFB.pieTitle,
                            width: '100%',
                            height: 300,
                            backgroundColor: '#f1f1f1',
                            colors: ['#91c690', '#cf3f35']

                        };
                        // Instantiate and draw our chart, passing in some options.
                        var chart = new google.visualization.PieChart(document.getElementById('afb-pie-chart'));
                        chart.draw(data, options);
                    }

                    //
                    // Create Bar chart
                    //
                    if( !result.ranking.length ){
                        $('#afb-bar-chart').removeClass('loading').append('<p class="no-data">' + AFB.noData + '</p>');
                    }else{
                        var table = [ ['Name', AFB.piePositive, AFB.pieNegative, { role: 'annotation' } ] ];
                        for( var i = 0; i < result.ranking.length; i++){
                            table.push([
                                result.ranking[i].post_title,
                                parseInt(result.ranking[i].positive),
                                parseInt(result.ranking[i].negative),
                                ''
                            ]);
                        }

                        var barOptions = {
                            width: '100%',
                            height: 300,
                            legend: { position: 'top', maxLines: 3 },
                            bar: { groupWidth: '20px' },
                            backgroundColor: '#f1f1f1',
                            colors: ['#91c690', '#cf3f35'],
                            isStacked: true
                        };
                        var barChart = new google.visualization.BarChart(document.getElementById('afb-bar-chart'));
                        barChart.draw(google.visualization.arrayToDataTable(table), barOptions);
                    }
                });
            }

        });


    }
})(jQuery);
