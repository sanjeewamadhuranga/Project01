import { registerTheme } from "echarts";
import utils from "@pay/falcon-theme/src/js/utils";

const grays = utils.getGrays();
const themeColors = utils.getColors();

registerTheme("default", {
  seriesCnt: 1,
  tooltip: {
    backgroundColor: grays.white,
    borderColor: grays["300"],
    textStyle: { color: themeColors.dark },
  },
  color: [
    themeColors.primary,
    themeColors.info,
    themeColors.success,
    "#fac858",
    "#ee6666",
    "#73c0de",
    "#3ba272",
    "#fc8452",
    "#9a60b4",
    "#ea7ccc",
  ],
  grid: {
    right: "28px",
    left: "80px",
    bottom: "15%",
    top: "5%",
  },
  axes: [
    {
      type: "all",
      animation: true,
      axisPointer: {
        lineStyle: {
          color: grays["300"],
          type: "dashed",
        },
      },
      splitNumber: 0,
      splitLine: { show: false },
      axisLine: {
        lineStyle: {
          color: utils.rgbaColor("#000", 0.01),
          type: "dashed",
        },
      },
      axisTick: { show: false },
      axisLabel: {
        color: grays["400"],
      },
    },
    {
      type: "value",
      axisPointer: { show: false },
      splitLine: {
        lineStyle: {
          color: grays["300"],
          type: "dashed",
        },
      },
      axisLabel: {
        show: true,
        color: grays["400"],
      },
      axisTick: { show: false },
      axisLine: { show: false },
    },
    {
      type: "time",
      splitNumber: 0,
      axisLabel: {
        rotate: 45,
      },
    },
  ],
  line: {
    itemStyle: {
      borderWidth: 2,
    },
    lineStyle: {
      width: 2,
    },
    symbolSize: 10,
    symbol: "emptyCircle",
    smooth: true,
  },
});
