import settings from "../settings";
import { formatDuration } from "./dates";

describe("formatDuration func", () => {
  const data = [
    {
      locale: "en",
      name: "shorter than a day",
      startDate: "2021-10-01T07:25:00",
      endDate: "2021-10-01T10:00:00",
      output: "2 hr 35 min",
    },
    {
      locale: "en",
      name: "longer than a day",
      startDate: "2021-10-01T07:25:00",
      endDate: "2021-10-04T10:00:00",
      output: "3 days 2 hr 35 min",
    },
    {
      locale: "vi",
      name: "shorter than a day",
      startDate: "2021-10-01T07:25:00",
      endDate: "2021-10-01T10:00:00",
      output: "2 giờ 35 phút",
    },
    {
      locale: "vi",
      name: "longer than a day",
      startDate: "2021-10-01T07:25:00",
      endDate: "2021-10-04T10:00:00",
      output: "3 ngày 2 giờ 35 phút",
    },
    {
      locale: "ar",
      name: "shorter than a day",
      startDate: "2021-10-01T07:25:00",
      endDate: "2021-10-01T10:00:00",
      output: "٢ س ٣٥ د",
    },
    {
      locale: "ar",
      name: "longer than a day",
      startDate: "2021-10-01T07:25:00",
      endDate: "2021-10-04T10:00:00",
      output: "٣ أيام ٢ س ٣٥ د",
    },
  ];

  describe.each(data)("Formats date ", (dataSet) => {
    it(`Duration ${dataSet.name} in ${dataSet.locale} locale`, () => {
      settings.userLocale = dataSet.locale;
      expect(formatDuration(dataSet.startDate, dataSet.endDate)).toBe(
        dataSet.output
      );
    });
  });
});
