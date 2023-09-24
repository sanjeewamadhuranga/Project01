import { DateTime } from "luxon";

export const getDateEnd = (date: string) => {
  return DateTime.fromISO(new Date(date).toISOString()).endOf("day").toJSDate();
};
