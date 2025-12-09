package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillSalesData

@Dao
interface SugarMillSalesDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: SugarMillSalesData)

    @Query("SELECT * FROM SugarMillSalesData")
    fun getAllSalesListData(): List<SugarMillSalesData?>?

    @Query("SELECT * FROM SugarMillSalesData WHERE sugarmill_id =:id")
    fun getAllSalesById(id: Int): List<SugarMillSalesData?>?

    @Query("SELECT * FROM SugarMillSalesData WHERE sugarmill_id =:id AND month=:month AND year=:year")
    fun getAllSalesById(id: Int, month: String, year: String): List<SugarMillSalesData?>?

    @Query("SELECT * FROM SugarMillSalesData WHERE month=:month AND year=:year")
    fun getAllSalesById(month: String, year: String): List<SugarMillSalesData?>?

    @Query("DELETE FROM SugarMillSalesData WHERE sugarmill_id =:id")
    fun deleteTableData(id: Int)
}