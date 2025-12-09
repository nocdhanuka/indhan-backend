package com.dhanuka.indhan.RegisterFarmerScreen


import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.farmerRegistrationMenu.FarmerRegistrationSubMenuModel
import kotlinx.android.synthetic.main.layout_menu_item.view.*

class FarmerRegistrationSubMenuAdapter(
    val context: Context,
    val subMenuList: List<FarmerRegistrationSubMenuModel>,
    private val mActivityContext: FarmerRegistrationMenuActivity
) : RecyclerView.Adapter<FarmerRegistrationSubMenuAdapter.ViewHolder>() {

    private var clickListener: SubMenuClickListener

    init {
        clickListener = mActivityContext
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((subMenuList != null) && (subMenuList.size > adapterPosition) && (adapterPosition != -1)) {
                    clickListener.onClickMenu(view, subMenuList[adapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: SubMenuClickListener) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_menu_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(
        holder: ViewHolder,
        position: Int
    ) {
        val catName = holder.itemView.tvMeCategoryName
        Glide.with(mActivityContext)
            .load(subMenuList[position].image)
            .into(holder.itemView.IvImage)
        catName.text = subMenuList[position].name
    }

    override fun getItemCount(): Int {
        return subMenuList.size
    }

    interface SubMenuClickListener {
        fun onClickMenu(view: View, subMenu: FarmerRegistrationSubMenuModel)
    }


}