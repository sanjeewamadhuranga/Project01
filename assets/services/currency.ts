import settings from "./settings";

export function formatMoney(amount: number, currency: string): string {
  return new Intl.NumberFormat(settings.userLocale, {
    style: "currency",
    currency,
  }).format(amount);
}

export function currencySymbol(currency: string): string {
  return (
    new Intl.NumberFormat(settings.userLocale, {
      style: "currency",
      currency,
    })
      .formatToParts()
      .filter((item) => item.type === "currency")[0]?.value ?? currency
  );
}
