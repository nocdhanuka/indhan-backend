package com.dhanuka.indhan.support

import com.dhanuka.indhan.support.model.SupportSubMenuBean
import java.util.*

interface SupportContractor {

    interface SupportView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: SupportSubMenuBean)
        fun failure(items: String)
    }

    interface SupportPresenter {

        fun onGetSubMenus(mSubMenus: HashMap<String, Any>)
    }

    interface SupportInteractor {

        fun ddSubMenu(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onSubMenuSuccess(items: SupportSubMenuBean)

            fun onSubmenuFailed(errorMessage: String)
        }
    }
}