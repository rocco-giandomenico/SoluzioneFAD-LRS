import XapiFilters from './XapiFilters'

export default class XapiActivitiesFilters extends XapiFilters {

    reset() {
        super.reset()
        this.id = null
        this.type = null
        this.name = null
    }

    empty() {
        return !this.id && !this.type && !this.name 
            && super.empty()
    }

    addParams(params) {
        this.addId(params)
        this.addType(params)
        this.addName(params)
    }

    addId(params) {
        if (!this.id) {
            return false
        }
        params.filters['uiId'] = this.id.trim()
        return true
    }

    addType(params) {
        if (!this.type) {
            return false
        }
        params.filters['uiType'] = this.type.trim()
        return true
    }

    addName(params) {
        if (!this.name) {
            return false
        }
        params.filters['uiName'] = this.name.trim()
        return true
    }
}
