import React, { useContext } from "react";
import { Button } from "react-bootstrap";
import TableContext from "../Table/TableContext";
import { useIntl } from "react-intl";
import axios from "axios";
import { toast } from "react-toastify";

export interface MigrationButtonProps {
  url: string;
  label: string;
}

const MigrationButton = (props: MigrationButtonProps) => {
  const table = useContext(TableContext);
  const intl = useIntl();
  const migrationAction = () => {
    axios
      .put(props.url)
      .then(() => {
        table.reload();
        toast.success(
          intl.formatMessage({ id: "configuration.migrations.actions.success" })
        );
      })
      .catch(() => {
        toast.error(
          intl.formatMessage({ id: "configuration.migrations.actions.error" })
        );
      });
  };
  return (
    <Button
      variant="falcon-default"
      size="sm"
      onClick={migrationAction}
      type="button"
    >
      {props.label}
    </Button>
  );
};

export default MigrationButton;
