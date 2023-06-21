// Chart code
const upColor = '#ec0000';
const upBorderColor = '#8A0000';
const downColor = '#00da3c';
const downBorderColor = '#008F28';

function splitData(rawData) {
    const categoryData = [];
    const values = [];
    for (var i = 0; i < rawData.length; i++) {
        categoryData.push(rawData[i].splice(0, 1)[0]);
        values.push(rawData[i]);
    }

    return {
        categoryData: categoryData,
        values: values
    };
}

function calculateMA(dayCount) {
    var result = [];
    for (var i = 0, len = dbData.length; i < len; i++) {
        if (i < dayCount) {
            result.push('-');
            continue;
        }
        var sum = 0;
        for (var j = 0; j < dayCount; j++) {
            sum += +dbData[i - j].close_price;
        }
        result.push(sum / dayCount);
    }
    return result;
}

// Render the chart
// Replace 'stockChartContainer' with the ID of the HTML element where you want to display the chart
var chart = echarts.init(document.getElementById('stockChartContainer'));

option = {
    title: {
        text: 'Stock Chart - ' + stockNo,
        left: 0
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'cross'
        }
    },
    legend: {
        data: ['日K', 'MA5', 'MA10', 'MA20', 'MA30']
    },
    grid: {
        left: '10%',
        right: '10%',
        bottom: '15%'
    },
    xAxis: {
        type: 'category',
        data: dbData.map(item => item.date),
        boundaryGap: false,
        axisLine: {
            onZero: false
        },
        splitLine: {
            show: false
        },
        min: 'dataMin',
        max: 'dataMax'
    },
    yAxis: {
        scale: true,
        splitArea: {
            show: true
        }
    },
    dataZoom: [{
            type: 'inside',
            start: 50,
            end: 100
        },
        {
            show: true,
            type: 'slider',
            top: '90%',
            start: 50,
            end: 100
        }
    ],
    series: [{
            name: '日K',
            type: 'candlestick',
            data: dbData.map(item => [item.open_price, item.close_price, item.min_price, item.max_price]),
            itemStyle: {
                color: upColor,
                color0: downColor,
                borderColor: upBorderColor,
                borderColor0: downBorderColor
            },
            markPoint: {
                label: {
                    formatter: function(param) {
                        return param != null ? Math.round(param.value) + '' : '';
                    }
                },
                tooltip: {
                    formatter: function(param) {
                        return param.name + '<br>' + (param.data.coord || '');
                    }
                }
            }
        },
        {
            name: 'MA5',
            type: 'line',
            data: calculateMA(5),
            smooth: true,
            lineStyle: {
                opacity: 0.5
            }
        },
        {
            name: 'MA10',
            type: 'line',
            data: calculateMA(10),
            smooth: true,
            lineStyle: {
                opacity: 0.5
            }
        },
        {
            name: 'MA20',
            type: 'line',
            data: calculateMA(20),
            smooth: true,
            lineStyle: {
                opacity: 0.5
            }
        },
        {
            name: 'MA30',
            type: 'line',
            data: calculateMA(30),
            smooth: true,
            lineStyle: {
                opacity: 0.5
            }
        }
    ]
};

chart.setOption(option);