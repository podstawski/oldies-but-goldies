qx.Class.define("Validate",
{
    statics :
    {
        PATTERN_EMAIL   : /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/,
        PATTERN_URL     : /([A-Za-z0-9])+:\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,

        number : function(errorMessage)
        {
            return function(value)
            {
                if (/\D/.test(value)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value is not a number"), [value]));
                }

                return true;
            }
        },

        email : function(errorMessage)
        {
            return function(value)
            {
                if (value && !Validate.PATTERN_EMAIL.test(value)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value is not a valid email address"), [value]));
                }

                return true;
            }
        },

        string : function(errorMessage)
        {
            return function(value)
            {
                value = value || "";

                if (value && typeof value !== "string" && !(value instanceof String)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value is not a string"), [value]));
                }

                return true;
            }
        },

        url : function(errorMessage)
        {
            return function(value)
            {
                if (value && !Validate.PATTERN_URL.test(value)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value is not an url"), [value]));
                }

                return true;
            }
        },

        color : function(errorMessage)
        {
            return function(value)
            {
                try {
                    qx.util.ColorUtil.stringToRgb(value);
                } catch (e) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value is not a valid color"), [value]));
                }
            }
        },

        inArray : function(array, errorMessage)
        {
            return function(value)
            {
                if (!qx.lang.Array.contains(array, value)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value is not in \"%2\""), [value, array]));
                }

                return true;
            }
        },

        range : function(from, to, errorMessage)
        {
            return function(value)
            {
                if (!(value >= from && value <= to)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value is not in the range from [%2, %3]"), [value, from, to]));
                }

                return true;
            }
        },

        regex : function(regex, errorMessage)
        {
            regex = new RegExp(regex);

            return function(value)
            {
                value = value || "";

                if (value && regex.test(value) === false) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value has invalid format"), [value]));
                }

                return true;
            }
        },

        alnum : function(errorMessage)
        {
            return function(value)
            {
                if (value && /\W/.test(value)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value must consist of alphanumeric characters"), [value]));
                }

                return true;
            }
        },

        greaterThan : function(min, errorMessage)
        {
            return function(value)
            {
                if (!(value > min)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value must be greater than %2"), [value, min]));
                }

                return true;
            }
        },

        greaterOrEqualThan : function(min, errorMessage)
        {
            return function(value)
            {
                if (!(value >= min)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage = errorMessage || "value must not be lower than %2"), [value, min]));
                }

                return true;
            }
        },

        lowerThan : function(max, errorMessage)
        {
            return function(value)
            {
                if (!(value < max)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value must be lower than %2"), [value, max]));
                }

                return true;
            }
        },

        lowerOrEqualThan : function(max, errorMessage)
        {
            return function(value)
            {
                if (!(value <= max)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value must not be greater than %2"), [value, max]));
                }

                return true;
            }
        },

        slength : function(min, max, errorMessage)
        {
            min = min || 0;
            max = max || Number.MAX_VALUE;

            return function(value)
            {
                if (!value) {
                    value = "";
                }

                if (!(value.length >= min && value.length <= max)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "value must be between %2 and %3 characters long"), [value, min, max]));
                }

                return true;
            }
        },

        pesel : function (errorMessage)
        {
            var factor = new Array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);

            return function (pesel)
            {
                if (!pesel) {
                    return true;
                }

                Validate.slength(11, 11, "pesel must have %2 chars")(pesel);
                Validate.number("pesel must contain only digits")(pesel);

                var checkSum = 0;
                for (var i = 0; i < 10; i++) {
                    checkSum += pesel.charAt(i) * factor[i];
                }

                if (!((10 - (checkSum % 10)) % 10 == pesel.charAt(10))) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "invalid pesel"), [pesel]));
                }

                return true;
            }
        },

        zipCode : function (errorMessage)
        {
            return function (value)
            {
                Validate.regex("^[0-9]{2}-[0-9]{3}$", errorMessage || "invalid zip code")(value);

                return true;
            }
        },

        nrb : function (errorMessage)
        {
            var factor = new Array(1, 10, 3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51, 25, 56, 75, 71, 31, 19, 93, 57);

            return function (value)
            {
                if (!value) {
                    return true;
                }

                Validate.regex("^[0-9 ]+$", "nrb must contain only digits and spaces")(value);

                var nrb = value.replace(/[^0-9]+/g, '');
                Validate.slength(26, 26, "nrb must contain %2 digits")(nrb);

                nrb = nrb + "2521";
                nrb = nrb.substr(2) + nrb.substr(0,2);
                var checkSum = 0;
                for (var i = 0; i < 30; i++) {
                    checkSum += nrb[29 - i] * factor[i];
                }
                if (!(checkSum % 97 == 1)) {
                    throw new qx.core.ValidationError("Validation Error", qx.lang.String.format(Tools["tr"](errorMessage || "invalid nrb"), [value]));
                }
                return true;
            }
        },

        phoneNumber : function (errorMessage)
        {
            return function (value)
            {
                Validate.regex("^[0-9]{9}$", errorMessage || "invalid phone number")(value);
                return true;
            }
        }
    }
});