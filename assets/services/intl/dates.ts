import DurationUnitFormat from "intl-unofficial-duration-unit-format";
import settings from "../settings";
import { format } from "date-fns";
import { DateFieldValue } from "../../models/form";

export function formatDuration(start: string, end: string): string {
  const duration = new DurationUnitFormat(settings.userLocale, {
    style: DurationUnitFormat.styles.SHORT,
    format: `{days} {hours} {minutes} {seconds}`,
  });

  return duration.format(
    (new Date(end).getTime() - new Date(start).getTime()) / 1000
  );
}

export function formatFromTo(val: DateFieldValue) {
  if (!val) return;
  return (
    format(val["min"], "yyyy/MM/dd") + " - " + format(val["max"], "yyyy/MM/dd")
  );
}
