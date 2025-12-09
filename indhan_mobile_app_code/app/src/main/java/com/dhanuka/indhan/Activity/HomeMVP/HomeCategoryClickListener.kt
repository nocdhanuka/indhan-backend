package com.dhanuka.indhan.Activity.HomeMVP

import android.view.View

import com.dhanuka.indhan.Activity.HomeMVP.Model.MenuBean

/**
 * Created by Shivam on 2/27/2018.
 */

interface HomeCategoryClickListener {
    fun onClick(view: View, menuBean: MenuBean)
}
