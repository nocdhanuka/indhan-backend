package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoSubMenu

@Dao
interface DemoSubMenuDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: List<DemoSubMenu>)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: DemoSubMenu)

    @Query("SELECT * FROM DemoSubMenu")
    fun getAllMenuData(): List<DemoSubMenu?>?

    @Query("DELETE FROM DemoSubMenu WHERE id =:id")
    fun deleteTableData(id: Int)

    @Query("DELETE FROM DemoSubMenu")
    fun deleteTableData()
}