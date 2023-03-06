class LogicError extends Error {
  constructor(args) {
    super(args);
    this.name = "LogicError";
  }
}
module.exports = LogicError;
