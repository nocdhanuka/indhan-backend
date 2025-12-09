package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.CategoryData

@Dao
interface CategoryDataDao {
    @Insert
    fun insert(data: CategoryData?): Long

    @get:Query("SELECT * FROM CategoryData")
    val allCategoryData: List<CategoryData?>?

    @Query("DELETE FROM CategoryData")
    fun deleteTableData()
}