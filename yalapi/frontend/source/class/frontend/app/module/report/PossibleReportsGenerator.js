qx.Class.define("frontend.app.module.report.PossibleReportsGenerator",
{
    type: "static",
    statics: {
        _config: {
            templates: {
                1:  'PresenceList',
                2:  'DoorList',
                3:  'Certificates',
                4:  'CertificatesReceiveConfirmation',
                5:  'LoginsReceiveConfirmation',
                6:  'TrainingMaterialsReceiveConfirmation',
                7:  'CourseSchedule',
                8:  'RegistrationForm',
                9:  'SurveyResults',
                10: 'NewStudent',
                11: 'PefsForAll',
                12: 'Ejournal'
            },
            groups: [
                {
                    params: ['group_id', 'course_id'],
                    templates: [1, 2, 3, 4, 5, 6, 7]
                },
                {
                    params: ['group_id'],
                    templates: [5]
                },
                {
                    params: ['course_id'],
                    templates: [7, 12]
                },
                {
                    params: ['survey_id', 'group_id'],
                    templates: [9]
                },
                {
                    params: ['course_id', 'user_id'],
                    templates: [8]
                },
                {
                    params: ['user_id'],
                    templates: [10]
                }
            ]
        },
        run: function(templateID, params) {
            var validInput = this._validateTemplateWithParams(templateID, params);
            if (validInput === false) {
                throw Error('Invalid input params for given template');
            }
            window.location.href = Urls.resolve('REPORTS', params);
        },
        findPossibleTemplates: function(params, exactMatch) {
            var possibleTemplates = {},
                templates = this._config.templates;
            for (var id in templates) {
                if (templates.hasOwnProperty(id) && (this._validateTemplateWithParams(id, params, exactMatch) === true)) {
                    possibleTemplates[id] = templates[id];
                }
            }
            return possibleTemplates;
        },
        /**
         *
         * @param templateID
         * @param params
         * @param exactMatch if set to true, templates with exact number of matching parameters will be considered valid
         */
        _validateTemplateWithParams: function(templateID, params, exactMatch) {
            var config = this._config.groups,
                paramsLength = qx.lang.Object.getLength(params),
                paramKeys = qx.lang.Object.getKeys(params);

            templateID = parseInt(templateID, 10);

            outer: for (var i = 0, len = config.length; i < len; i++) {
                var group = config[i];

                //RB performance
                if (exactMatch === true && paramsLength !== group.params.length) {
                    continue;
                }

                //RB check, if templates id is valid for that group
                if (!qx.lang.Array.contains(group.templates, templateID)) {
                    continue;
                }

                //RB none of passed values can't be null or undefined
                for (var key in params) {
                    if (!params[key]) {
                        continue outer;
                    }
                }

                //RB there is not compare function for array, so if we exclude items and get empty array, they are the same
                var groupParamsCopy = group.params.slice(0);
                if (qx.lang.Array.exclude(groupParamsCopy, paramKeys).length === 0) {
                    return true;
                }
            }
            return false;
        }
    }
});