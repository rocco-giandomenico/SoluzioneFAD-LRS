// Filters.
import XapiContextFilters from "./filters/XapiContextFilters";
import XapiStatementsFilters from "./filters/XapiStatementsFilters";
import XapiActivitiesFilters from "./filters/XapiActivitiesFilters";
import XapiAgentsFilters from "./filters/XapiAgentsFilters";
import XapiActivityProfilesFilters from "./filters/XapiActivityProfilesFilters";
import XapiAgentProfilesFilters from "./filters/XapiAgentProfilesFilters";
import XapiStatesFilters from "./filters/XapiStatesFilters";
import XapiVerbsFilters from "./filters/XapiVerbsFilters";
import XapiActivityTypesFilters from "./filters/XapiActivityTypesFilters";
import XapiStatementCategoriesFilters from "./filters/XapiStatementCategoriesFilters";
import XapiLogsFilters from "./filters/XapiLogsFilters";

// Loaders.
import XapiLoader from "./loaders/XapiLoader";
import XapiStatementsLoader from "./loaders/XapiStatementsLoader";

export default {
    install(Vue) {
        const contextFilters = new XapiContextFilters()

        const filters = {
            context: contextFilters,
            statements: new XapiStatementsFilters(contextFilters),
            activities: new XapiActivitiesFilters(contextFilters),
            agents: new XapiAgentsFilters(contextFilters),
            activityProfiles: new XapiActivityProfilesFilters(contextFilters),
            agentProfiles: new XapiAgentProfilesFilters(contextFilters),
            states: new XapiStatesFilters(contextFilters),
            verbs: new XapiVerbsFilters(contextFilters),
            activityTypes: new XapiActivityTypesFilters(contextFilters),
            statementCategories: new XapiStatementCategoriesFilters(contextFilters),
            logs: new XapiLogsFilters(contextFilters),
        }

        Vue.prototype.$xapi = {
            filters,
            loaders: {
                statements: new XapiStatementsLoader('/trax/api/front/xapi/ext/statements', filters.statements),
                activities: new XapiLoader('/trax/api/front/xapi/ext/activities', filters.activities),
                agents: new XapiLoader('/trax/api/front/xapi/ext/agents', filters.agents),
                activityProfiles: new XapiLoader('/trax/api/front/xapi/ext/activity_profiles', filters.activityProfiles),
                agentProfiles: new XapiLoader('/trax/api/front/xapi/ext/agent_profiles', filters.agentProfiles),
                states: new XapiLoader('/trax/api/front/xapi/ext/states', filters.states),
                verbs: new XapiLoader('/trax/api/front/xapi/ext/verbs', filters.verbs),
                activityTypes: new XapiLoader('/trax/api/front/xapi/ext/activity_types', filters.activityTypes),
                statementCategories: new XapiLoader('/trax/api/front/xapi/ext/statement_categories', filters.statementCategories),
                logs: new XapiLoader('/trax/api/front/xapi/ext/logs', filters.logs),
            }
        }
    }
}
