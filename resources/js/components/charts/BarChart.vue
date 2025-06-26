<template>
    <div>
      <BarChart :chart-data="chartData" :chart-options="chartOptions" />
    </div>
  </template>
  
  <script>
  import { defineComponent, reactive } from "vue";
  import { BarChart } from "vue-chart-3";
  import { Chart, registerables } from "chart.js";
  
  Chart.register(...registerables);
  
  export default defineComponent({
    components: {
      BarChart
    },
    props: {
    chartData: Object, 
    chartOptions: Object, 
  },
  });
  </script>
  