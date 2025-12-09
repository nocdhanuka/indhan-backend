package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.MenuScreenActivity.dbmodel.ProductBean;

import java.util.List;

@Dao
public interface ProductDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(ProductBean data);

    @Query("SELECT * FROM ProductBean WHERE crop_id =:cropId")
    List<ProductBean> getAllProductList(int cropId);


    @Query("DELETE FROM ProductBean")
    void deleteTableData();


    @Query("SELECT * FROM ProductBean WHERE crop_id =:cropId AND product = :productName")
    ProductBean getProductIdFromName(int cropId, String productName);
}