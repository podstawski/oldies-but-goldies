qx.Class.define("Urls",
{
    extend : qx.core.Object,

    statics :
    {
        "COURSES"          : "index.php/courses",
        "COURSE_UNITS"     : "index.php/course-units",
        "PROJECTS"         : "index.php/projects",
        "TRAINING_CENTERS" : "index.php/training-centers",
        "QUIZZES"          : "index.php/quizzes",
        "QUIZ_USERS"       : "index.php/quiz-users",
        "QUIZ_SCORES"      : "index.php/quiz-scores",
        "SURVEY"           : "index.php/surveys",
        "REPORT_TEMPLATES" : "index.php/report-templates",
        "REPORTS"          : "index.php/reports",
        "LOGIN"            : "index.php/login",
        "LOGOUT"           : "index.php/logout",
        "LESSONS"          : "index.php/lessons",
        "USERS"            : "index.php/users",
        "COACHES"          : "index.php/coaches",
        "GROUPS"           : "index.php/groups",
        "GROUP_USERS"      : "index.php/group-users",
        "EXAMS"            : "index.php/exams",
        "ROOMS"            : "index.php/rooms",
        "SURVEY_GROUPS"    : "index.php/survey-groups",
        "RESOURCE_TYPES"   : "index.php/resource-types",
        "RESOURCES"        : "index.php/resources",
        "GROUP_EXAMS"      : "index.php/group-exams",
        "GROUP_GRADES"     : "index.php/group-grades",
        "GROUP_PRESENCE"   : "index.php/group-presence",
        "GROUP_COURSES"    : "index.php/group-courses",
        "COURSE_SCHEDULE"  : "index.php/course-schedule",
        "MESSAGES"         : "index.php/messages",
        "DOWNLOAD"         : "index.php/download",
        "DASHBOARD"        : "index.php/dashboard",
        "DASHBOARD_EVENTS" : "index.php/dashboard-events",
        "USER_PROFILE"     : "index.php/user-profile",
        "USER_ACCOUNT"     : "index.php/user-account",
        "POLAND"           : "index.php/poland",
        "ACL"              : "index.php/acl",
        "AUTH_OPEN_ID"     : "index.php/auth/open-id",
        "USER_INFO"        : "index.php/user-info",
        "PROJECT_LEADERS"  : "index.php/project-leaders",

        "RENEW_ACCESS_TOKEN" : "index.php/auth/oauth?renew-access-token=true",

        "GOOGLE_APPS_RETRIEVE_ALL_USERS"  : "index.php/google-apps/retrieve-all-users",
        "GOOGLE_APPS_RETRIEVE_ALL_GROUPS" : "index.php/google-apps/retrieve-all-groups",
        "GOOGLE_APPS_SYNC_GROUP"          : "index.php/google-apps/sync-group",

        /**
         * Resolves url from given alias.
         * Converts params into query string.
         *
         * @param {String} alias
         * @param {null|Map} params
         * @param {null|String} pk
         */
        resolve : function(alias, params)
        {
            var url;
            if (Urls[alias] !== undefined) {
                url = Urls[alias];
            } else  {
                url = alias;
            }

            if (params)
            {
                if (!(/\D/.test(params))) {
                    params = { id : params }
                }
                
                if (params.id !== undefined) {
                    url = url + "/" + params.id;
                    delete params.id;
                }

                url = qx.util.Uri.appendParamsToUrl(url, params);
            }

            //RB if we running source version, go one level up
            var prefix = (qx.core.Environment.get("qx.debug") === true && !qx.lang.String.startsWith(alias, "../")) ? '../' : '';

            return prefix + url;
        },
        getURLParam : function(name) {
            name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
            var regexS = "[\\?&]" + name + "=([^&#]*)";
            var regex = new RegExp( regexS );
            var results = regex.exec( window.location.href );
            if( results == null )
              return "";
            else
              return results[1];
        }
    }
});
