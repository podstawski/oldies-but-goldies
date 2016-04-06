qx.Class.define("frontend.app.module.wizard.FirstSteps", {
    extend : frontend.app.module.wizard.Abstract,

    members :
    {
        _steps :
        {
            "frontend.app.form.Project" : "Dodaj projekt",
            "frontend.app.form.training_center.Add" : "Dodaj ośrodek szkoleniowy",
            "frontend.app.form.Course" : "Dodaj szkolenie"
        }
    }
});