import React from "react";
import { Platform } from "../../models/Platform";

interface Props {
  platform: Platform;
}

const PlatFormIcon = (props: Props) => {
  const classMap = {
    [Platform.ios]: "fab fa-apple",
    [Platform.android]: "fab fa-android text-success",
    [Platform.web]: "fas fa-globe-asia text-info",
  };

  return (
    <div className="text-center">
      <span className={classMap[props.platform]} />
    </div>
  );
};

export default PlatFormIcon;
