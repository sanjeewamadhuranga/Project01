import React, { PropsWithChildren, ReactNode } from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

interface Props {
  justifyContent?: string;
  alignItems?: string;
  alignContent?: string;
  inline?: string;
  wrap?: string;
  className?: string;
  tag?: any;
  children?: ReactNode;
  breakpoint?: string;
  direction?: string;
  [rest: string]: any;
}

const Flex = ({
  justifyContent,
  alignItems,
  alignContent,
  inline,
  wrap,
  className,
  tag: Tag = "div",
  children,
  breakpoint,
  direction,
  ...rest
}: PropsWithChildren<Props>) => {
  return (
    <Tag
      className={classNames(
        {
          [`d-${breakpoint ? breakpoint + "-" : ""}flex`]: !inline,
          [`d-${breakpoint ? breakpoint + "-" : ""}inline-flex`]: inline,
          [`flex-${direction}`]: direction,
          [`justify-content-${justifyContent}`]: justifyContent,
          [`align-items-${alignItems}`]: alignItems,
          [`align-content-${alignContent}`]: alignContent,
          [`flex-${wrap}`]: wrap,
        },
        className
      )}
      {...rest}
    >
      {children}
    </Tag>
  );
};

Flex.propTypes = {
  children: PropTypes.node,
  justifyContent: PropTypes.string,
  inline: PropTypes.bool,
  alignItems: PropTypes.string,
  alignContent: PropTypes.string,
  wrap: PropTypes.string,
  className: PropTypes.string,
  tag: PropTypes.string,
  breakpoint: PropTypes.string,
  direction: PropTypes.string,
};

export default Flex;
