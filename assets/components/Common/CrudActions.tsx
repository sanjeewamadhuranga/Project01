import React, { PropsWithChildren } from "react";
import { ButtonGroup } from "react-bootstrap";
import DeleteButton from "../Modals/DeleteButton";
import EditButton from "../Common/EditButton";
import Routing from "../../services/routing";
import JustifyEnd from "./JustifyEnd";

export interface Props {
  id: string;
  title: string;
  routeNamePrefix?: string;
  extraParams?: Record<string, string | number | boolean>;
}

const CrudActions = (props: PropsWithChildren<Props>) => {
  return (
    <JustifyEnd>
      <ButtonGroup>
        <EditButton
          editLink={Routing.generate(`${props.routeNamePrefix}_edit`, {
            id: props?.id,
            ...props?.extraParams,
          })}
        />
        <DeleteButton
          deleteUrl={Routing.generate(`${props.routeNamePrefix}_delete`, {
            id: props?.id,
            ...props?.extraParams,
          })}
          title={props.title}
        />
        {props.children}
      </ButtonGroup>
    </JustifyEnd>
  );
};

export default CrudActions;
