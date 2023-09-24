import { useMemo } from "react";
import { FilterType } from "../../../helpers/entries";

export function useAppliedFilterButton(type: FilterType) {
  const intlPrefixes = useMemo(() => {
    switch (type) {
      case "merchant":
        return {
          label: "merchant.merchants",
          statusPrefix: "merchant",
        };
      case "remittance":
        return {
          label: "remittance",
          statusPrefix: "remittance",
        };
      case "federatedIdentities":
        return {
          label: "onboarding.federatedIdentities",
          statusPrefix: "",
        };
      case "transaction":
      case "merchantTransaction":
      default:
        return {
          label: "transaction",
          statusPrefix: "transaction",
        };
    }
  }, [type]);

  return { intlPrefixes };
}
