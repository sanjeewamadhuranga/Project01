import React from "react";
import { defineMessages, FormattedMessage } from "react-intl";

interface Props {
  count: number;
  name: string;
}

export default function EntryLabel({ name, count }: Props) {
  const messages = defineMessages({
    item: {
      id: `${name}.count`,
      defaultMessage: `${name}.count`,
    },
  });

  return <FormattedMessage values={{ count }} {...messages.item} />;
}
