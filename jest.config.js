module.exports = {
    resetMocks: false,
    testEnvironment: "jsdom",
    setupFilesAfterEnv: ["<rootDir>/assets/config/importJestDOM.ts"],
    coverageDirectory: "coverage/js",
    collectCoverageFrom: ["assets/**/*.{js,jsx,ts,tsx}"],
    transformIgnorePatterns: ["<rootDir>/node_modules"],
    testPathIgnorePatterns: ["<rootDir>/vendor"],
    moduleNameMapper: {
        "^.+\\.(css|styl|less|sass|scss|png|jpg|ttf|woff|woff2|svg|mp4)$":
            "jest-transform-stub",
    },
};
