export const flow = {
  name: "DUMMY Genie Flow",
  key: "DUMY_GENIE_FLOW",
  default: true,
  locales: [
    { code: "en", name: "English" },
    { code: "si", name: "Sinhala" },
    { code: "ta", name: "Tamil" },
  ],
  sections: [
    {
      key: "DUMMY_USER_INFO_EXT",
      position: 1,
      title: "Tell us about you",
      description: "We need your personal information so we can contact you.",
      screens: [
        {
          key: "USER_NAME",
          title: "Your name",
          description:
            "Please enter your name as it appears on your ID, we will need this to verify your business at a later stage.",
          position: "1",
          titleTranslations: {
            en: "Your name",
            ta: "Your name(tamil)",
            si: "Your name(sinhala)",
          },
          descriptionTranslations: {
            en: "Please enter your name as it appears on your ID",
            ta: "Please enter your name as it appears on your ID(tamil)",
            si: "Please enter your name as it appears on your ID(sinhala)",
          },
        },
        {
          key: "USER_EMAIL",
          title: "What's your email address?",
          description: "This will be used to verify your account.",
          position: "2",
        },
        {
          key: "DIALOG_NATIONAL_IDENTITY",
          title: "National identity card",
          description: "Help us confirm your identity",
          position: "3",
        },
        {
          key: "REVIEW_DETAILS",
          title:
            "Before you continue, please ensure the following details are correct",
          description: "Review details",
          position: "4",
        },
      ],
      id: "61c04b2fb7e1b452332ff493",
    },
    {
      key: "DUMMY_BUSINESS_INFO",
      position: 2,
      title: "Tell us about your business",
      description:
        "We’re required by law to have an understanding of your company.",
      screens: [
        {
          key: "DIALOG_TYPE_OF_SERVICE",
          title: "What services do you require?",
          description: "What services do you require?",
          position: "1",
        },
        {
          key: "BUSINESS_TYPE",
          title: "What type of business is it?",
          description: "What type of business is it?",
          position: "2",
        },
        {
          key: "REGISTERED_NAME",
          title: "Your business name",
          description:
            "Name (as in public records). If you are a sole trader then just enter your name.",
          position: "3",
        },
        {
          key: "REGISTERED_ADDRESS",
          title: "Enter address",
          description: "Enter address",
          position: "4",
        },
        {
          key: "BUSINESS_INFO",
          title: "Nature of business",
          description:
            "This information helps us understand how our product will be used to take payments.",
          position: "5",
        },
        {
          key: "TRADING_NAME",
          title: "Your commercial name",
          description:
            "If your commercial name is the same as your registered name, just select ‘Same as business name’ below the field.",
          position: "6",
        },
        {
          key: "TRADING_ADDRESS",
          title: "Commercial address",
          description:
            "This is where your clients, bank, agents, suppliers etc send you correspondence.",
          position: "7",
        },
        {
          key: "DIALOG_COMPANY_NUMBER",
          title: "Business registration number",
          description:
            "Name (as in public records). If you are a sole trader then just enter your name.",
          position: "8",
        },
        {
          key: "MCC",
          title: "Which of these best categorise your business?",
          description: "Which of these best categorise your business?",
          position: "9",
        },
        {
          key: "PHONE",
          title: "Your business phone number",
          description: "Your business phone number",
          position: "10",
        },
        {
          key: "BUSINESS_EMAIL",
          title: "Business email",
          description: "Your business email",
          position: "11",
        },
        {
          key: "CURRENCY",
          title: "Your business currency",
          description: "Your business currency",
          position: "12",
        },
        {
          key: "REVIEW_DETAILS",
          title:
            "Before you continue, please ensure the following details are correct",
          description: "Review details",
          position: "13",
        },
      ],
      id: "61c04b2fb7e1b452332ff494",
    },
    {
      key: "DUMMY_BANK_ACC",
      position: 3,
      title: "Bank Account",
      description: "Add Bank Account",
      screens: [
        {
          key: "DIALOG_BANK_ACCOUNT_SCREEN",
          title: "Your application is being reviewed by our team",
          description: "Your application is being reviewed by our team",
          position: "1",
        },
      ],
      id: "61c04b2fb7e1b452332ff495",
    },
    {
      key: "DUMMY_REVIEW",
      position: 4,
      title: "Reviewing your application",
      description:
        "Our team will review your application and will contact you within one working day.",
      screens: [
        {
          key: "REVIEW",
          title: "Your application is being reviewed by our team",
          description: "Your application is being reviewed by our team",
          position: "1",
        },
      ],
      id: "61c04b2fb7e1b452332ff495",
    },
  ],
  id: "61c04a7689158a765a6c6010",
  createdAt: null,
  updatedAt: null,
  deleted: false,
};

export const sectionsList = {
  count: 3,
  sections: [
    {
      id: "1",
      key: "STANDARD",
      title: "Standard",
      description:
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec mattis.",
    },
    {
      id: "2",
      key: "KYC",
      title: "KYC",
      description:
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec mattis.",
    },
    {
      id: "3",
      key: "OPEN_BANKING",
      title: "Open Banking",
      description:
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec mattis.",
    },
  ],
};

export const labels = [
  { text: "New", type: "success" },
  { text: "Goal", type: "primary" },
  { text: "Enhancement", type: "info" },
  { text: "Bug", type: "danger" },
  { text: "Documentation", type: "secondary" },
  { text: "Helper", type: "warning" },
];

export const initialFlowTestData = {
  screens: {
    "task-1": { id: "task-1", content: "Take out the garbage" },
    "task-2": { id: "task-2", content: "Watch my favorite show" },
    "task-3": { id: "task-3", content: "Charge my phone" },
    "task-4": { id: "task-4", content: "Cook dinner" },
  },
  sections: {
    "column-1": {
      id: "column-1",
      title: "To do",
      taskIds: ["task-1", "task-2", "task-3", "task-4"],
    },
    "column-2": {
      id: "column-2",
      title: "In progress",
      taskIds: [],
    },
    "column-3": {
      id: "column-3",
      title: "Done",
      taskIds: [],
    },
  },
  columnOrder: ["column-1", "column-2", "column-3"],
};
