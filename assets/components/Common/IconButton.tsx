import React, { PropsWithChildren } from "react";
import PropTypes from "prop-types";
import classNames from "classnames";
import { Button } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { Transform } from "@fortawesome/fontawesome-svg-core";

interface Props {
  icon: any;
  iconAlign?: string;
  iconClassName?: string;
  transform?: string | Transform;
  children: React.ReactNode | JSX.Element;
  [rest: string]: any;
}

const IconButton = ({
  icon,
  iconAlign = "left",
  iconClassName,
  transform,
  children,
  ...rest
}: PropsWithChildren<Props>) => (
  <Button {...rest}>
    {iconAlign === "right" && children}
    <FontAwesomeIcon
      icon={icon}
      className={classNames(iconClassName, {
        "me-1": children && iconAlign === "left",
        "ms-1": children && iconAlign === "right",
      })}
      transform={transform}
    />
    {iconAlign === "left" && children}
  </Button>
);

IconButton.propTypes = {
  ...Button.propTypes,
  icon: PropTypes.any.isRequired,
  children: PropTypes.any,
  iconAlign: PropTypes.oneOf(["left", "right"]),
  iconClassName: PropTypes.string,
  transform: PropTypes.string,
};

export default IconButton;
