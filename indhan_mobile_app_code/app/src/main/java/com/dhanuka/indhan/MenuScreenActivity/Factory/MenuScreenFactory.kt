package com.dhanuka.indhan.MenuScreenActivity.Factory

import com.dhanuka.indhan.MenuScreenActivity.Model.MenuDBModel

/**
 * Created by John on 5/3/2018.
 */
interface MenuScreenFactory {
    interface MenuViewListener {
        fun onGetMenuFromDBResult(menu: ArrayList<MenuDBModel>)

    }

    interface MenuPresenterListener {
        fun getMenuFromDBRequest()
        fun getMenuFromDBResultSuccess(menu: ArrayList<MenuDBModel>)
    }

    interface MenuInteractorListener {
        fun getMenuFromDB(listener: MenuScreenFactory.MenuPresenterListener)
    }

}