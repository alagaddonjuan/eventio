<div id="panel-analytics" class="hidden h-full">
    <div class="p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Analytics & Insights</h3>
        
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                <div class="flex items-center text-sm font-semibold text-slate-500 mb-2">
                    <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Page Views
                </div>
                <div class="text-3xl font-extrabold text-slate-800">{{ number_format($event->page_views) }}</div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                <div class="flex items-center text-sm font-semibold text-slate-500 mb-2">
                    <svg class="w-4 h-4 mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Conversion Rate
                </div>
                <div class="text-3xl font-extrabold text-slate-800">{{ $conversionRate }}%</div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm mb-8">
            <h4 class="text-sm font-semibold text-slate-700 mb-4">Registration Trend</h4>
            <div id="trendChart"></div>
        </div>

        <!-- Ticket Type Distribution -->
        @if($ticketSales->count() > 0)
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <h4 class="text-sm font-semibold text-slate-700 mb-4">Sales by Ticket Type</h4>
            <div id="ticketChart"></div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data for Trend Chart
    const trendData = @json($guestsOverTime);
    const trendCategories = trendData.map(item => item.date);
    const trendSeries = trendData.map(item => item.total);

    if (trendCategories.length > 0) {
        var trendOptions = {
            series: [{
                name: 'RSVPs/Tickets',
                data: trendSeries
            }],
            chart: {
                height: 250,
                type: 'area',
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#0ea5e9'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: trendCategories,
                labels: { style: { colors: '#94a3b8' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8' } }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: 'light'
            }
        };

        var chart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
        chart.render();
    } else {
        document.querySelector("#trendChart").innerHTML = '<p class="text-sm text-slate-400 text-center py-8">No registration data available yet.</p>';
    }

    // Data for Ticket Donut Chart
    const ticketData = @json($ticketSales);
    if(ticketData && ticketData.length > 0) {
        const ticketLabels = ticketData.map(item => item.ticket ? item.ticket.name : 'Free RSVP');
        const ticketSeries = ticketData.map(item => item.total);
        
        var ticketOptions = {
            series: ticketSeries,
            labels: ticketLabels,
            chart: {
                type: 'donut',
                height: 250,
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ec4899'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { show: true },
                            value: { show: true }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            tooltip: { theme: 'light' }
        };

        var tChart = new ApexCharts(document.querySelector("#ticketChart"), ticketOptions);
        tChart.render();
    }
});
</script>
