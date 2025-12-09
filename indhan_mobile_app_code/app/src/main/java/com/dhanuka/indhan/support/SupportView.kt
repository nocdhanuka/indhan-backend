package com.dhanuka.indhan.support

import com.dhanuka.indhan.support.model.SupportSubMenuBean

interface SupportView {

    fun onDataCompleteFromApi(subMenu: SupportSubMenuBean?)
    fun onDataErrorFromApi(throwable: Throwable)
}