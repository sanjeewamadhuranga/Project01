import React from "react";
import Lottie from "react-lottie-player";

const ICONS = {
  "success-check": require("../../resources/lottie/success-check.json"),
  "barbed-success-check": require("../../resources/lottie/barbed-success-check.json"),
};

type IconName = keyof typeof ICONS;

type Props = {
  name: IconName;
  loop?: boolean;
  autoplay?: boolean;
};

export default function AnimatedIcon({
  autoplay = true,
  loop = false,
  name,
}: Props) {
  return <Lottie loop={loop} animationData={ICONS[name]} play={autoplay} />;
}
