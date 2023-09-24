import React from "react";
import ReactEchartsCore from "echarts-for-react";
import { formatMoney } from "../../services/currency";
import { EChartsOption } from "echarts/types/dist/echarts";
import utils from "@pay/falcon-theme/src/js/utils";

interface Props {
  data: Array<TransactionSum>;
  currency: string;
}

interface TransactionSum {
  date: string;
  amount: number;
}

export default function SalesOverTimeChart(props: Props): JSX.Element {
  const chartOptions: EChartsOption = {
    tooltip: {
      triggerOn: "mousemove",
      trigger: "axis",
      formatter: (params) =>
        `${params[0].axisValueLabel}: ${formatMoney(
          params[0].data.amount,
          props.currency
        )}`,
    },
    xAxis: {
      type: "time",
      splitNumber: 0,
    },
    yAxis: {
      type: "value",
      axisLabel: {
        formatter: (amount: number) => formatMoney(amount, props.currency),
      },
    },
    series: [
      {
        encode: {
          x: "date",
          y: "amount",
          itemName: "Date",
          tooltip: ["Transaction amount"],
        },
        type: "line",
        areaStyle: {
          color: {
            x: 0,
            y: 0,
            x2: 0,
            y2: 1,
            colorStops: [
              {
                offset: 0,
                color: utils.rgbaColor(utils.getColors().primary, 0.3),
              },
              {
                offset: 1,
                color: utils.rgbaColor(utils.getColors().primary, 0),
              },
            ],
            global: false,
          },
        },
      },
    ],
    dataset: {
      source: props.data.map((item) => ({
        amount: item.amount / 100,
        date: item.date,
      })),
    },
  };

  return (
    <ReactEchartsCore
      option={chartOptions}
      theme="default"
      style={{ minHeight: "25rem" }}
    />
  );
}
