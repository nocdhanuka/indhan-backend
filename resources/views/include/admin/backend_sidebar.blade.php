
<aside class="main-sidebar left-side_green">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar ">
        <?php
        $role_sess      = \Session::get('login_role_id');
        $adminid        = \Session::get('adminid');
        $admin_details  = \DB::table('admin')
                            ->where('role_id', $role_sess)
                            ->get();
        // var_dump($admin_details);
        if(count($admin_details) > 0)
        {
            foreach ($admin_details as $details) {
                $role = $details->username;
            }
            $pag_id     = \Session::get('permission');
        }
        else
        {
            $admin_details  = \DB::table('branch_admin')
                            ->where('id', $adminid)
                            ->get();

            foreach ($admin_details as $details) {
                $role = $details->user_name;
                $pag_ids = \DB::table('role_permission')
                            ->where('role_id', $details->role_id)
                            ->get();
                $pag_id = $pag_ids[0]->page_id;
            }   
            
            //print_r($pag_id);die;

        }
       
        ?>
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{asset('assets/images/default_profile.jpg')}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo $role; ?></p>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header left-side_green">MAIN NAVIGATION</li>
            <?php
            //$role_sess=\Session::get('login_role_id');
            //$pag_id     = \Session::get('permission');
            $pg_id      = explode(',', $pag_id);
             //echo "<pre>"; print_r($pg_id); die;
            $select_pgs = \DB::table('pages')
                            ->whereIn('p_id', $pg_id)
                            ->get();
            foreach ($select_pgs as $row) {
                $page_name[] = $row->pages;
            }
            // dd($page_name);
            // echo "<pre>";print_r($page_name);die;
            ?>
            <?php 
                if($role_sess == '8' && in_array('dashboard', $page_name)){?>
                    <li class="treeview {{ Request::is('admin/dashboard') ? 'active' : '' }} ">
                        <a href="{{ URL::to('admin/dashboard') }}">
                            <i class="fa fa-home"></i>
                            <span>Dashboard</span>
                            <i class="fa pull-right"></i>
                        </a>
                    </li>
                <?php
                }
                else if ($role_sess == '5' || $role_sess == '7') {?>
                    <li class="treeview {{ Request::is('admin/dashboard') ? 'active' : '' }} ">
                        <a href="{{ URL::to('admin/dashboard') }}">
                            <i class="fa fa-home"></i>
                            <span>Dashboard</span>
                            <i class="fa pull-right"></i>
                        </a>
                    </li>
                <?php    
                }
                
            ?>
                
            
            
            <?php
            if (in_array('add-language', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('language/add-language') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('language/add-language') }}">
                        <i class="fa fa-language"></i>
                        <span>Language List</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li>
                <?php
            }

            if (in_array('demo-request', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('demo/demo-request') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('demo/demo-request') }}">
                        <i class="fa fa-language"></i>
                        <span>Demo Summary</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li>
                <?php 
            }

            if (in_array('add-district', $page_name) || in_array('district-list', $page_name) || in_array('add-tehsil', $page_name) || in_array('tehsil-list', $page_name)) { ?>
            <li class="treeview
                 {{ Request::is('district/add-district') ? 'active' : '' }}
                 {{ Request::is('tehsil/add-tehsil') ? 'active' : '' }}
                 {{ Request::is('village/add-village') ? 'active' : '' }}
                 {{ Request::is('district/district-list') ? 'active' : '' }}
                 {{ Request::is('tehsil/tehsil-list') ? 'active' : '' }}
				 {{ Request::is('territory/add-territory') ? 'active' : '' }															
                ">
                <a href="javascript:void(0)">
                    <i class="fa fa-map-marker"></i>
                    <span>Location</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                    if (in_array('add-district', $page_name) || in_array('district-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('district/add-district') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('district/add-district') }}">
                                <i class="fa fa-globe"></i>
                                <span>District</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('add-tehsil', $page_name) || in_array('tehsil-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('tehsil/add-tehsil') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('tehsil/add-tehsil') }}">
                                <i class="fa fa-globe"></i>
                                <span>Tehsil</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('add-village', $page_name) || in_array('village-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('village/add-village') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('village/add-village') }}">
                                <i class="fa fa-globe"></i>
                                <span>Village</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    ?>
                </ul>
            </li>
            <?php
            }
			if (in_array('add-district', $page_name) || in_array('add-market', $page_name) || in_array('add-territory', $page_name) || in_array('tehsil-list', $page_name)) { ?>
                <li class="treeview
                
                     {{ Request::is('village/add-village') ? 'active' : '' }}
                     {{ Request::is('district/district-list') ? 'active' : '' }}
                     {{ Request::is('market/add-market') ? 'active' : '' }}
                     {{ Request::is('territory/add-territory') ? 'active' : '' }}
                    ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-map-marker"></i>
                        <span>Data collection</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php
                        if (in_array('add-territory', $page_name) || in_array('territory-list', $page_name)) { ?>
                            <li class="treeview {{ Request::is('territory/add-territory') ? 'subactive' : '' }}">
                                <a href="{{ URL::to('territory/add-territory') }}">
                                    <i class="fa fa-globe"></i>
                                    <span>Territory</span>
                                    <i class="fa pull-right"></i>
                                </a>
                            </li>                
                            <?php
                        }
                        if (in_array('add-market', $page_name) || in_array('market-list', $page_name)) { ?>
                            <li class="treeview {{ Request::is('market/add-market') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('market/add-market') }}">
                                    <i class="fa fa-globe"></i>
                                    <span>Market</span>
                                    <i class="fa pull-right"></i>
                                </a>
                            </li>                
                            <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
                }

            if (in_array('add-product', $page_name) || in_array('product-list', $page_name) || in_array('add-crop', $page_name) || in_array('crop-list', $page_name) || in_array('add-category', $page_name) || in_array('category-list', $page_name) || in_array('add-recommendation', $page_name) || in_array('recommendation-list', $page_name) || in_array('stage-list', $page_name) || in_array('add-stage', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('product/add-product') ? 'active' : '' }}
                 {{ Request::is('crop/add-crop') ? 'active' : '' }}
                 {{ Request::is('category/add-category') ? 'active' : '' }}
                 {{ Request::is('recommendation/add-recommendation') ? 'active' : '' }}
                 {{ Request::is('crop/stage-list') ? 'active' : '' }}
                ">
                <a href="javascript:void(0)">
                    <i class="fa fa-tasks"></i>
                    <span>Product</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                    if (in_array('add-product', $page_name) || in_array('product-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('product/add-product') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('product/add-product') }}">
                                <i class="fa fa-magic"></i>
                                <span>Product</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('add-crop', $page_name) || in_array('crop-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('crop/add-crop') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('crop/add-crop') }}">
                                <i class="glyphicon glyphicon-leaf"></i>
                                <span>Crop</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('add-category', $page_name) || in_array('category-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('category/add-category') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('category/add-category') }}">
                                <i class="glyphicon glyphicon-asterisk"></i>
                                <span>Category</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('add-recommendation', $page_name) || in_array('recommendation-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('recommendation/add-recommendation') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('recommendation/add-recommendation') }}">
                                <i class="glyphicon glyphicon-retweet"></i>
                                <span>Product Crop Relation</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('stage-list', $page_name) || in_array('add-stage', $page_name)) { ?>
                        <li class="treeview {{ Request::is('crop/stage-list') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('crop/stage-list') }}">
                                <i class="glyphicon glyphicon-asterisk"></i>
                                <span>Stage</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    ?>
                </ul>
            </li>
            <?php
            }
            if (in_array('add-zonal-manager', $page_name) || in_array('add-regional-manager', $page_name)) {
            ?>

            <li class="treeview
                 {{ Request::is('zonal/add-zonal-manager') ? 'active' : '' }}
                 {{ Request::is('regional/add-regional-manager') ? 'active' : '' }}
                 {{ Request::is('regional/regional-manager-list') ? 'active' : '' }}
                 {{ Request::is('salesregional/add-sales-regional-manager') ? 'active' : '' }}
                 {{ Request::is('salesregional/sales-regional-manager-list') ? 'active' : '' }}
                 {{ Request::is('area/add-area-manager') ? 'active' : '' }}
                 {{ Request::is('area/area-manager-list') ? 'active' : '' }}
                 {{ Request::is('salesarea/add-sales-area-manager') ? 'active' : '' }}
                 {{ Request::is('salesarea/sales-area-manager-list') ? 'active' : '' }}
                 {{ Request::is('admin/add-bdo') ? 'active' : '' }}
                 {{ Request::is('admin/bdo-list') ? 'active' : '' }}
                 {{ Request::is('admin/deputation') ? 'active' : '' }}
                 {{ Request::is('admin/add-dd') ? 'active' : '' }}
                 {{ Request::is('admin/dd-list') ? 'active' : '' }}
                 {{ Request::is('admin/add-branch-admin') ? 'active' : '' }}
                 {{ Request::is('admin/branch-list') ? 'active' : '' }}
                ">
                <a href="javascript:void(0)">
                    <i class="fa fa-users"></i>
                    <span>Team</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                    if (in_array('add-marketing-supervisor', $page_name) || in_array('marketing-supervisor-list', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('supervisor/add-marketing-supervisor') ? 'active' : '' }}
                                {{ Request::is('supervisor/marketing-supervisor-list') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>Marketing Supervisor</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-marketing-supervisor', $page_name) && $role_sess != '8') { ?>
                                    <li class="{{ Request::is('supervisor/add-marketing-supervisor') ? 'subactive' : '' }}"><a href="{{ URL::to('supervisor/add-marketing-supervisor') }}"><i class="fa fa-arrow-right"></i> Marketing Supervisor</a></li>
                                <?php
                                }
                                if (in_array('sales-regional-manager-list', $page_name)) { ?>
                                    <li class="{{ Request::is('salesregional/marketing-supervisor-list') ? 'subactive' : '' }}"><a href="{{ URL::to('supervisor/marketing-supervisor-list') }}"><i class="fa fa-arrow-right"></i>  List of Marketing Supervisor</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    } 
                    if (in_array('add-zonal-manager', $page_name) || in_array('zonal-manager-list', $page_name) && $role_sess != '8') { ?>
                        <li class="treeview {{ Request::is('zonal/add-zonal-manager') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('zonal/add-zonal-manager') }}">
                                <i class="fa fa-user"></i>
                                <span>ZSH/S3</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('add-regional-manager', $page_name) || in_array('regional-manager-list', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('regional/add-regional-manager') ? 'active' : '' }}
                                {{ Request::is('regional/regional-manager-list') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>SME</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-regional-manager', $page_name) && $role_sess != '8') { ?>
                                    <li class="{{ Request::is('regional/add-regional-manager') ? 'subactive' : '' }}"><a href="{{ URL::to('regional/add-regional-manager') }}"><i class="fa fa-arrow-right"></i> Add SME</a></li>
                                <?php
                                }
                                if ((in_array('regional-manager-list', $page_name))) { ?>
                                    <li class="{{ Request::is('regional/regional-manager-list') ? 'subactive' : '' }}"><a href="{{ URL::to('regional/regional-manager-list') }}"><i class="fa fa-arrow-right"></i> List of SME</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }
                    if (in_array('add-sales-regional-manager', $page_name) || in_array('sales-regional-manager-list', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('salesregional/add-sales-regional-manager') ? 'active' : '' }}
                                {{ Request::is('salesregional/sales-regional-manager-list') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>DGM/S2</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-sales-regional-manager', $page_name) && $role_sess != '8') { ?>
                                    <li class="{{ Request::is('salesregional/add-sales-regional-manager') ? 'subactive' : '' }}"><a href="{{ URL::to('salesregional/add-sales-regional-manager') }}"><i class="fa fa-arrow-right"></i> DGM/S2</a></li>
                                <?php
                                }
                                if ((in_array('sales-regional-manager-list', $page_name))) { ?>
                                    <li class="{{ Request::is('salesregional/sales-regional-manager-list') ? 'subactive' : '' }}"><a href="{{ URL::to('salesregional/sales-regional-manager-list') }}"><i class="fa fa-arrow-right"></i>  List of DGM/S2</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }                        
                    if (in_array('add-area-manager', $page_name) || in_array('area-manager-list', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('area/add-area-manager') ? 'active' : '' }}
                                {{ Request::is('area/area-manager-list') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>Not In Use ( BD S1 )</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-area-manager', $page_name) && $role_sess != '8') { ?>
                                    <li class="{{ Request::is('area/add-area-manager') ? 'subactive' : '' }}"><a href="{{ URL::to('area/add-area-manager') }}"><i class="fa fa-arrow-right"></i> BD S1</a></li>
                                <?php
                                }
                                if ((in_array('area-manager-list', $page_name))) { ?>
                                    <li class="{{ Request::is('area/area-manager-list') ? 'subactive' : '' }}"><a href="{{ URL::to('area/area-manager-list') }}"><i class="fa fa-arrow-right"></i>  List of BD S1</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }                        
                    if (in_array('add-sales-area-manager', $page_name) || in_array('sales-area-manager-list', $page_name)) { ?>
                        <li class="treeview
                            {{ Request::is('salesarea/add-sales-area-manager') ? 'active' : '' }}
                            {{ Request::is('salesarea/sales-area-manager-list') ? 'active' : '' }}
                           ">
                        <a href="javascript:void(0)">
                            <i class="fa fa-user"></i>
                            <span>ASM/S1</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if (in_array('add-sales-area-manager', $page_name) && $role_sess != '8') { ?>
                                <li class="{{ Request::is('salesarea/add-sales-area-manager') ? 'subactive' : '' }}"><a href="{{ URL::to('salesarea/add-sales-area-manager') }}"><i class="fa fa-arrow-right"></i> ASM/S1</a></li>
                            <?php
                            }
                            if ((in_array('sales-area-manager-list', $page_name))) { ?>
                                <li class="{{ Request::is('salesarea/sales-area-manager-list') ? 'subactive' : '' }}"><a href="{{ URL::to('salesarea/sales-area-manager-list') }}"><i class="fa fa-arrow-right"></i>  List of ASM/S1</a></li>
                            <?php
                            } ?>
                        </ul>
                    </li>
                    <?php 
                    }
                    if (in_array('add-bdo', $page_name) || in_array('bdo-list', $page_name ) || in_array('deputation', $page_name )) { ?>
                        <li class="treeview
                                {{ Request::is('admin/add-bdo') ? 'active' : '' }}
                                {{ Request::is('admin/bdo-list') ? 'active' : '' }}
                                {{ Request::is('admin/deputation') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>Front Liner,BD/SL/CP FL</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-bdo', $page_name) && $role_sess != '8') { ?>
                                    <li class="{{ Request::is('admin/add-bdo') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/add-bdo') }}"><i class="fa fa-arrow-right"></i> Add New FL</a></li>
                                <?php
                                }
                                if ((in_array('bdo-list', $page_name))) { ?>
                                    <li class="{{ Request::is('admin/bdo-list') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/bdo-list') }}"><i class="fa fa-arrow-right"></i>  List of FL</a></li>
                                <?php
                                } 
                                if ((in_array('deputation', $page_name))) { ?>
                                    <?php if($role_sess == '8'){?>
                                        <li class="{{ Request::is('organogram/deputationorn') ? 'subactive' : '' }}"><a href="{{ URL::to('organogram/deputationorn') }}"><i class="fa fa-arrow-right"></i>  List of Deputation</a></li>
                                    <?php }
                                    else{?>
                                        <li class="{{ Request::is('admin/deputation') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/deputation') }}"><i class="fa fa-arrow-right"></i>  List of Deputation</a></li>
                                    <?php } ?>
                                    
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    } if (in_array('add-gen', $page_name) || in_array('gen-list', $page_name )) { ?> 
                        <li class="treeview
                                {{ Request::is('admin/add-gen') ? 'active' : '' }}
                                {{ Request::is('admin/gen-list') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>General</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-gen', $page_name)) { ?>
                                    <li class="{{ Request::is('admin/add-gen') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/add-gen') }}"><i class="fa fa-arrow-right"></i> Add New General</a></li>
                                <?php
                                }
                                if ((in_array('gen-list', $page_name))) { ?>
                                    <li class="{{ Request::is('admin/gen-list') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/gen-list') }}"><i class="fa fa-arrow-right"></i>  List of General</a></li>
                                <?php
                                } 
                                 ?>                                    
                            </ul>
                        </li>                
                        <?php
                    } if (in_array('add-dd', $page_name) || in_array('dd-list', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('admin/add-dd') ? 'active' : '' }}
                                {{ Request::is('admin/dd-list') ? 'active' : '' }}

                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>Dhanuka Doctor</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-dd', $page_name) && $role_sess != '8') { ?>
                                    <li class="{{ Request::is('admin/add-dd') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/add-dd') }}"><i class="fa fa-arrow-right"></i> Add New DD</a></li>
                                <?php
                                }
                                if ((in_array('dd-list', $page_name))) { ?>
                                    <li class="{{ Request::is('admin/dd-list') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/dd-list') }}"><i class="fa fa-arrow-right"></i>  List of DD</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }  
                    if($role_sess == '5'){
                        if (in_array('add-branch-admin', $page_name) || in_array('branch-list', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('admin/add-branch-admin') ? 'active' : '' }}
                                {{ Request::is('admin/branch-list') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>Branch Admin</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ((in_array('add-branch-admin', $page_name))) { ?>
                                    <li class="{{ Request::is('admin/add-branch-admin') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/add-branch-admin') }}"><i class="fa fa-arrow-right"></i> Add Branch Admin</a></li>
                                <?php
                                }
                                if ((in_array('branch-list', $page_name))) { ?>
                                    <li class="{{ Request::is('admin/branch-list') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/branch-list') }}"><i class="fa fa-arrow-right"></i>  List of Branch Admin</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }

                    }
                    
                    
                    ?>
                </ul>
            </li>  
                
            <?php } //organogram
            if (in_array('add-zonal-manager-orn', $page_name) || in_array('add-regional-manager-orn', $page_name)) {
            ?>

            <li class="treeview
                 {{ Request::is('zonal/zonal-manager-list-orn') ? 'active' : '' }}
                 {{ Request::is('regional/add-regional-manager-orn') ? 'active' : '' }}
                 {{ Request::is('regional/regional-manager-list-orn') ? 'active' : '' }}
                 {{ Request::is('salesregional/add-sales-regional-manager-orn') ? 'active' : '' }}
                 {{ Request::is('salesregional/sales-regional-manager-list-orn') ? 'active' : '' }}
                 {{ Request::is('area/add-area-manager-orn') ? 'active' : '' }}
                 {{ Request::is('area/area-manager-list-orn') ? 'active' : '' }}
                 {{ Request::is('salesarea/add-sales-area-manager-orn') ? 'active' : '' }}
                 {{ Request::is('salesarea/sales-area-manager-list-orn') ? 'active' : '' }}
                 {{ Request::is('admin/add-bdo-orn') ? 'active' : '' }}
                 {{ Request::is('admin/bdo-list-orn') ? 'active' : '' }}
                 {{ Request::is('admin/deputation') ? 'active' : '' }}
                 {{ Request::is('admin/add-dd-orn') ? 'active' : '' }}
                 {{ Request::is('admin/dd-list-orn') ? 'active' : '' }}
                ">
                <a href="javascript:void(0)">
                    <i class="fa fa-users"></i>
                    <span>Organogram</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                    if (in_array('add-zonal-manager-orn', $page_name) || in_array('zonal-manager-list-orn', $page_name)) { ?>
                        <li class="treeview {{ Request::is('zonal/zonal-manager-list-orn') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('zonal/zonal-manager-list-orn') }}">
                                <i class="fa fa-user"></i>
                                <span>ZSH</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('add-regional-manager-orn', $page_name) || in_array('regional-manager-list-orn', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('regional/add-regional-manager-orn') ? 'active' : '' }}
                                {{ Request::is('regional/regional-manager-list-orn') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>RBDM</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-regional-manager-orn', $page_name)) { ?>
                                    <li class="{{ Request::is('regional/add-regional-manager-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('regional/add-regional-manager-orn') }}"><i class="fa fa-arrow-right"></i> Add RBDM</a></li>
                                <?php
                                }
                                if ((in_array('regional-manager-list-orn', $page_name))) { ?>
                                    <li class="{{ Request::is('regional/regional-manager-list-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('regional/regional-manager-list-orn') }}"><i class="fa fa-arrow-right"></i> List of RBDM</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }
                    if (in_array('add-sales-regional-manager-orn', $page_name) || in_array('sales-regional-manager-list-orn', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('salesregional/add-sales-regional-manager-orn') ? 'active' : '' }}
                                {{ Request::is('salesregional/sales-regional-manager-list-orn') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>RSM</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-sales-regional-manager-orn', $page_name)) { ?>
                                    <li class="{{ Request::is('salesregional/add-sales-regional-manager-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('salesregional/add-sales-regional-manager-orn') }}"><i class="fa fa-arrow-right"></i> RSM</a></li>
                                <?php
                                }
                                if ((in_array('sales-regional-manager-list-orn', $page_name))) { ?>
                                    <li class="{{ Request::is('salesregional/sales-regional-manager-list-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('salesregional/sales-regional-manager-list-orn') }}"><i class="fa fa-arrow-right"></i>  List of RSM</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }                        
                    if (in_array('add-area-manager-orn', $page_name) || in_array('area-manager-list-orn', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('area/add-area-manager-orn') ? 'active' : '' }}
                                {{ Request::is('area/area-manager-list-orn') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>BD S1</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-area-manager-orn', $page_name)) { ?>
                                    <li class="{{ Request::is('area/add-area-manager-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('area/add-area-manager-orn') }}"><i class="fa fa-arrow-right"></i> BD S1</a></li>
                                <?php
                                }
                                if ((in_array('area-manager-list-orn', $page_name))) { ?>
                                    <li class="{{ Request::is('area/area-manager-list-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('area/area-manager-list-orn') }}"><i class="fa fa-arrow-right"></i>  List of BD S1</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }                        
                    if (in_array('add-sales-area-manager-orn', $page_name) || in_array('sales-area-manager-list-orn', $page_name)) { ?>
                    <li class="treeview
                            {{ Request::is('salesarea/add-sales-area-manager-orn') ? 'active' : '' }}
                            {{ Request::is('salesarea/sales-area-manager-list-orn') ? 'active' : '' }}
                           ">
                        <a href="javascript:void(0)">
                            <i class="fa fa-user"></i>
                            <span>ASM</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if (in_array('add-sales-area-manager-orn', $page_name)) { ?>
                                <li class="{{ Request::is('salesarea/add-sales-area-manager-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('salesarea/add-sales-area-manager-orn') }}"><i class="fa fa-arrow-right"></i> ASM</a></li>
                            <?php
                            }
                            if ((in_array('sales-area-manager-list-orn', $page_name))) { ?>
                                <li class="{{ Request::is('salesarea/sales-area-manager-list-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('salesarea/sales-area-manager-list-orn') }}"><i class="fa fa-arrow-right"></i>  List of ASM</a></li>
                            <?php
                            } ?>
                        </ul>
                    </li>
                    <?php 
                    }if (in_array('add-gen', $page_name) || in_array('gen-list', $page_name )) { ?>
                        <li class="treeview
                                {{ Request::is('admin/add-gen') ? 'active' : '' }}
                                {{ Request::is('admin/gen-list') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>General</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-gen', $page_name) && $role_sess != '8') { ?>
                                    <li class="{{ Request::is('admin/add-gen') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/add-gen') }}"><i class="fa fa-arrow-right"></i> Add New General</a></li>
                                <?php
                                }
                                if ((in_array('gen-list', $page_name))) { ?>
                                    <li class="{{ Request::is('admin/gen-list') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/gen-list') }}"><i class="fa fa-arrow-right"></i>  List of General</a></li>
                                <?php
                                } 
                                 ?>                                    
                            </ul>
                        </li>                
                        <?php
                    }
                    if (in_array('add-bdo-orn', $page_name) || in_array('bdo-list-orn', $page_name) || in_array('deputation', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('organogram/add-bdo-orn') ? 'active' : '' }}
                                {{ Request::is('organogram/bdo-list-orn') ? 'active' : '' }}
                                {{ Request::is('organogram/deputationorn') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>FL</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ((in_array('add-bdo-orn', $page_name))) { ?>
                                    <li class="{{ Request::is('organogram/add-bdo-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('organogram/add-bdo-orn') }}"><i class="fa fa-arrow-right"></i> Add New FL</a></li>
                                <?php
                                }
                                if ((in_array('bdo-list-orn', $page_name))) { ?>
                                    <li class="{{ Request::is('organogram/bdo-list-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('organogram/bdo-list-orn') }}"><i class="fa fa-arrow-right"></i>  List of FL</a></li>
                                <?php
                                } if ((in_array('deputation', $page_name))) { ?>
                                    <li class="{{ Request::is('organogram/deputation') ? 'subactive' : '' }}"><a href="{{ URL::to('organogram/deputationorn') }}"><i class="fa fa-arrow-right"></i>  List of Deputation</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    } if (in_array('add-dd-orn', $page_name) || in_array('dd-list-orn', $page_name)) { ?>
                        <li class="treeview
                                {{ Request::is('organogram/add-dd-orn') ? 'active' : '' }}
                                {{ Request::is('organogram/dd-list-orn') ? 'active' : '' }}
                               ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>DD</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ((in_array('add-dd-orn', $page_name))) { ?>
                                    <li class="{{ Request::is('organogram/add-dd-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('organogram/add-dd-orn') }}"><i class="fa fa-arrow-right"></i> Add New DD</a></li>
                                <?php
                                }
                                if ((in_array('dd-list-orn', $page_name))) { ?>
                                    <li class="{{ Request::is('organogram/dd-list-orn') ? 'subactive' : '' }}"><a href="{{ URL::to('organogram/dd-list-orn') }}"><i class="fa fa-arrow-right"></i>  List of DD</a></li>
                                <?php
                                } ?>
                            </ul>
                        </li>                
                        <?php
                    }  
                    ?>
                </ul>
            </li>  
                
            <?php }
            if (in_array('add-department-wise', $page_name) || in_array('add-team-wise', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('permission/add-department-wise') ? 'active' : '' }}
                    {{ Request::is('permission/add-zonal-wise') ? 'active' : '' }}
                    {{ Request::is('permission/add-regional-wise') ? 'active' : '' }}
                    {{ Request::is('permission/add-sales-regional-wise') ? 'active' : '' }}
                    {{ Request::is('permission/add-area-wise') ? 'active' : '' }}
                    {{ Request::is('permission/add-sales-area-wise') ? 'active' : '' }}
                    {{ Request::is('permission/add-bdo-wise') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Permissions</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (in_array('add-department-wise', $page_name)) { ?>
                            <li class="{{ Request::is('permission/add-department-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-department-wise') }}"><i class="fa fa-user"></i></i> Department Wise</a></li>
                        <?php
                        }
                        ?>
                        <li class="treeview
                            {{ Request::is('permission/add-zonal-wise') ? 'active' : '' }}
                            {{ Request::is('permission/add-regional-wise') ? 'active' : '' }}
                            {{ Request::is('permission/add-sales-regional-wise') ? 'active' : '' }}
                            {{ Request::is('permission/add-area-wise') ? 'active' : '' }}
                            {{ Request::is('permission/add-sales-area-wise') ? 'active' : '' }}
                            {{ Request::is('permission/add-bdo-wise') ? 'active' : '' }}
                            {{ Request::is('permission/add-supervisor-wise') ? 'active' : '' }}
                           ">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>Team Wise</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (in_array('add-zonal-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-zonal-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-zonal-wise') }}"><i class="fa fa-arrow-right"></i> ZSH/S3</a></li>
                                <?php
                                }
                                if (in_array('add-regional-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-regional-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-regional-wise') }}"><i class="fa fa-arrow-right"></i> SME</a></li>
                                <?php
                                }
                                if (in_array('add-sales-regional-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-sales-regional-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-sales-regional-wise') }}"><i class="fa fa-arrow-right"></i> DGM/S2</a></li>
                                <?php
                                }
                                if (in_array('add-area-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-area-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-area-wise') }}"><i class="fa fa-arrow-right"></i> Not In Use ( BD S1 )</a></li>
                                <?php
                                }
                                if (in_array('add-sales-area-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-sales-area-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-sales-area-wise') }}"><i class="fa fa-arrow-right"></i> ASM/S1</a></li>
                                <?php
                                }
                                if (in_array('add-bdo-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-bdo-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-bdo-wise') }}"><i class="fa fa-arrow-right"></i> Front Liner,BD/SL/CP FL</a></li>
                                <?php
                                }
                                if (in_array('add-dd-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-dd-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-dd-wise') }}"><i class="fa fa-arrow-right"></i> Dhanuka Doctor</a></li>
                                <?php }
                                if (in_array('add-branch-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-branch-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-branch-wise') }}"><i class="fa fa-arrow-right"></i> Branch Admin</a></li>
                                <?php
                                }
                                if (in_array('add-supervisor-wise', $page_name)) { ?>
                                    <li class="{{ Request::is('permission/add-supervisor-wise') ? 'subactive' : '' }}"><a href="{{ URL::to('permission/add-supervisor-wise') }}"><i class="fa fa-arrow-right"></i> Marketing Supervisor</a></li>
                                <?php
                                }
                                ?>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php
            }
            
            if (in_array('tickets-list', $page_name) || in_array('open-tickets', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('admin/open-tickets') ? 'active' : '' }}
                 {{ Request::is('admin/close-tickets') ? 'active' : '' }}
                 {{ Request::is('admin/tickets-list') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Tickets</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('open-tickets', $page_name))) { ?>
                            <li class="{{ Request::is('admin/open-tickets') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/open-tickets') }}"><i class="fa fa-arrow-right"></i> Open Tickets</a></li>
                        <?php
                        }
                        if ((in_array('closed-tickets', $page_name))) { ?>
                            <li class="{{ Request::is('admin/close-tickets') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/close-tickets') }}"><i class="fa fa-arrow-right"></i> Closed Tickets</a></li>
                        <?php
                        }
                        if ((in_array('tickets-list', $page_name))) { ?>
                            <li class="{{ Request::is('admin/tickets-list') ? 'subactive' : '' }}"><a href="{{ URL::to('admin/tickets-list') }}"><i class="fa fa-arrow-right"></i>  All Tickets</a></li>
                        <?php 
                        } ?>
                    </ul>
                </li>
                <?php
            }

            if (in_array('product-wise-farmer', $page_name) || in_array('product-wise-invited-farmer', $page_name) || in_array('acpfarmer', $page_name) || in_array('acpvisitlist', $page_name)) 
                { ?>
            <li class="treeview
                 {{ Request::is('admin/product-wise-farmer') ? 'active' : '' }}
                 {{ Request::is('admin/product-wise-invited-farmer') ? 'active' : '' }}
                 {{ Request::is('admin/acpfarmer') ? 'active' : '' }}
                ">
                <a href="javascript:void(0)">
                    <i class="fa fa-user"></i>
                    <span>Farmer</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                    if (in_array('product-wise-farmer', $page_name)) { ?>
                        <li class="treeview {{ Request::is('admin/product-wise-farmer') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('admin/product-wise-farmer') }}">
                                <i class="fa fa-user"></i>
                                <span>Registed Farmers</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('product-wise-invited-farmer', $page_name)) { ?>
                        <li class="treeview {{ Request::is('admin/product-wise-invited-farmer') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('admin/product-wise-invited-farmer') }}">
                                <i class="fa fa-user"></i>
                                <span>Invited Farmers</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('acpfarmer', $page_name)) { ?>
                        <li class="treeview {{ Request::is('admin/acpfarmer') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('admin/acpfarmer') }}">
                                <i class="fa fa-user"></i>
                                <span>ACP Farmer List</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('acpvisitlist', $page_name)) { ?>
                        <li class="treeview {{ Request::is('admin/acpvisitlist') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('admin/acpvisitlist') }}">
                                <i class="fa fa-user"></i>
                                <span>ACP Visit List</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    ?>
                    <li class="treeview {{ Request::is('admin/ifcfarmer') ? 'subactive' : '' }}">
                        <a href="{{ URL::to('admin/ifcfarmer') }}">
                            <i class="fa fa-user"></i>
                            <span>IFC Farmer List</span>
                            <i class="fa pull-right"></i>
                        </a>
                    </li>
                </ul>
            </li>

                <?php
            }
            
            if (in_array('customer-queries', $page_name) || in_array('sale-queries', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('customer/customer-queries') ? 'active' : '' }}
                 {{ Request::is('customer/sale-queries') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Queries</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('customer-queries', $page_name))) { ?>
                            <li class="{{ Request::is('customer/customer-queries') ? 'subactive' : '' }}"><a href="{{ URL::to('customer/customer-queries') }}"><i class="fa fa-arrow-right"></i> Customer Queries</a></li>
                        <?php
                        }
                        if ((in_array('sale-queries', $page_name))) { ?>
                            <li class="{{ Request::is('customer/sale-queries') ? 'subactive' : '' }}"><a href="{{ URL::to('customer/sale-queries') }}"><i class="fa fa-arrow-right"></i> Sale Queries</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }
            
            
            
            
            

            if (in_array('participation-list', $page_name) || in_array('participation-plan-list', $page_name) || in_array('myatpplan', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('selfparticipation/participation-list') ? 'active' : '' }}
                 {{ Request::is('selfparticipation/participation-plan-list') ? 'active' : '' }}
                 {{ Request::is('selfparticipation/myatpplan') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Self Participation</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('participation-plan-list', $page_name))) { ?>
                            <li class="{{ Request::is('selfparticipation/participation-plan-list') ? 'subactive' : '' }}"><a href="{{ URL::to('selfparticipation/participation-plan-list') }}"><i class="fa fa-arrow-right"></i> Plan List</a></li>
                        <?php
                        }
                        if ((in_array('participation-list', $page_name))) { ?>
                            <li class="{{ Request::is('selfparticipation/participation-list') ? 'subactive' : '' }}"><a href="{{ URL::to('selfparticipation/participation-list') }}"><i class="fa fa-arrow-right"></i> Attended Plans</a></li>
                            
                        <?php
                        }
                        if ((in_array('myatpplan', $page_name))) { ?>
                            <li class="{{ Request::is('selfparticipation/myatpplan') ? 'subactive' : '' }}"><a href="{{ URL::to('selfparticipation/myatpplan') }}"><i class="fa fa-arrow-right"></i> My ATP Plans</a></li>
                            
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }
            if (in_array('chemical', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('chemical/districtwisedata') ? 'active' : '' }}
                    {{ Request::is('chemical/dccropwisedata') ? 'active' : '' }}
                    {{ Request::is('chemical/dccompanywisedata') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Chemical Section</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php /*if (in_array('add-class', $page_name)) { ?>
                            <li class="{{ Request::is('class/add-class') ? 'subactive' : '' }}"><a href="{{ URL::to('class/add-class') }}"><i class="fa fa-arrow-right"></i></i> Class</a></li>
                        <?php
                        } if (in_array('add-formulation', $page_name)) {
                        ?>
                            <li class="{{ Request::is('formulation/add-formulation') ? 'subactive' : '' }}"><a href="{{ URL::to('formulation/add-formulation') }}"><i class="fa fa-arrow-right"></i></i> Formulation</a></li>
                        <?php
                        } if (in_array('add-foodcategory', $page_name)) {
                        ?>
                            <li class="{{ Request::is('foodcategory/add-foodcategory') ? 'subactive' : '' }}"><a href="{{ URL::to('foodcategory/add-foodcategory') }}"><i class="fa fa-arrow-right"></i></i> Food Category</a></li>
                        <?php
                        } if (in_array('add-foodsubcategory', $page_name)) {
                        ?>
                            <li class="{{ Request::is('foodsubcategory/add-foodsubcategory') ? 'subactive' : '' }}"><a href="{{ URL::to('foodsubcategory/add-foodsubcategory') }}"><i class="fa fa-arrow-right"></i></i> Food SubCategory</a></li>
                        <?php
                        } if (in_array('add-compcompany', $page_name)) {
                        ?>
                            <li class="{{ Request::is('compcompany/add-compcompany') ? 'subactive' : '' }}"><a href="{{ URL::to('compcompany/add-compcompany') }}"><i class="fa fa-arrow-right"></i></i> Competitor Company</a></li>
                        <?php
                        }*/
                        ?>
                        <li class="{{ Request::is('chemical/districtwisedata') ? 'subactive' : '' }}"><a href="{{ URL::to('chemical/districtwisedata') }}"><i class="fa fa-arrow-right"></i></i>All District Wise Data List</a></li>
                        <li class="{{ Request::is('chemical/dccropwisedata') ? 'subactive' : '' }}"><a href="{{ URL::to('chemical/cropwisedata') }}"><i class="fa fa-arrow-right"></i></i>All Crop Wise Data List</a></li>
                        <li class="{{ Request::is('chemical/dccompanywisedata') ? 'subactive' : '' }}"><a href="{{ URL::to('chemical/companywisedata') }}"><i class="fa fa-arrow-right"></i></i>All Company Wise Data List</a></li>
                    </ul>
                </li>
            <?php
            }

            if (in_array('distributer', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('distributer/distributer-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('distributer/distributer-list') }}">
                        <i class="fa fa-phone-square"></i>
                        <span>Distributor</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li>
                <?php
            }

            if (in_array('testimonial-list', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('testimonial/testimonial-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('testimonial/testimonial-list') }}">
                        <i class="fa fa-phone-square"></i>
                        <span>Testimonial</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li>
                <?php
            }
            
            
            

            if (in_array('syllabus-list', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('syllabus/syllabus-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('syllabus/syllabus-list') }}">
                        <i class="fa fa-phone-square"></i>
                        <span>Syllabus</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li>
                <?php
            }
            
            
            

            if (in_array('atp-list', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('atp/atp-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('atp/atp-list') }}">
                        <i class="fa fa-phone-square"></i>
                        <span>ATP List</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li>
                <?php
            }
            
            
            if (    in_array('bdo-efficiency', $page_name) || 
                    in_array('area-efficiency', $page_name) || 
                    in_array('regional-efficiency', $page_name) || 
                    in_array('zonal-efficiency', $page_name) || 
                    in_array('sales-area-efficiency', $page_name) || 
                    in_array('sales-regional-efficiency', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('efficiency/bdo-efficiency') ? 'active' : '' }}
                 {{ Request::is('efficiency/area-efficiency') ? 'active' : '' }}
                 {{ Request::is('efficiency/regional-efficiency') ? 'active' : '' }}
                 {{ Request::is('efficiency/zonal-efficiency') ? 'active' : '' }}
                 {{ Request::is('efficiency/sales-area-efficiency') ? 'active' : '' }}
                 {{ Request::is('efficiency/sales-regional-efficiency') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Efficiency</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php 
                        if ((in_array('zonal-efficiency', $page_name))) { ?>
                            <li class="{{ Request::is('efficiency/zonal-efficiency') ? 'subactive' : '' }}"><a href="{{ URL::to('efficiency/zonal-efficiency') }}"><i class="fa fa-arrow-right"></i> Zonal Managers</a></li>
                        <?php
                        }
                        if ((in_array('regional-efficiency', $page_name))) { ?>
                            <li class="{{ Request::is('efficiency/regional-efficiency') ? 'subactive' : '' }}"><a href="{{ URL::to('efficiency/regional-efficiency') }}"><i class="fa fa-arrow-right"></i> Regional Managers</a></li>
                        <?php
                        }
                        if ((in_array('sales-regional-efficiency', $page_name))) { ?>
                            <li class="{{ Request::is('efficiency/sales-regional-efficiency') ? 'subactive' : '' }}"><a href="{{ URL::to('efficiency/sales-regional-efficiency') }}"><i class="fa fa-arrow-right"></i> Sales Regional Managers</a></li>
                        <?php
                        }
                        if ((in_array('area-efficiency', $page_name))) { ?>
                            <li class="{{ Request::is('efficiency/area-efficiency') ? 'subactive' : '' }}"><a href="{{ URL::to('efficiency/area-efficiency') }}"><i class="fa fa-arrow-right"></i> Area Managers</a></li>
                        <?php
                        }
                        if ((in_array('sales-area-efficiency', $page_name))) { ?>
                            <li class="{{ Request::is('efficiency/sales-area-efficiency') ? 'subactive' : '' }}"><a href="{{ URL::to('efficiency/sales-area-efficiency') }}"><i class="fa fa-arrow-right"></i> Sales Area Managers</a></li>
                        <?php
                        }
                        if ((in_array('bdo-efficiency', $page_name))) { ?>
                            <li class="{{ Request::is('efficiency/bdo-efficiency') ? 'subactive' : '' }}"><a href="{{ URL::to('efficiency/bdo-efficiency') }}"><i class="fa fa-arrow-right"></i> BDO</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }
            if (in_array('dd-earning', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('ddearning/dd-earning') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('ddearning/dd-earning') }}">
                        <i class="fa fa-phone-square"></i>
                        <span>DD Earning</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('distributor-list', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('distributor/distributor-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('distributor/distributor-list') }}">
                        <i class="fa fa-phone-square"></i>
                        <span>Distributor & Retailers</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('call-list', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('call/call-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('call/call-list') }}">
                        <i class="fa fa-phone-square"></i>
                        <span>Calls</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            
            if (in_array('season-list', $page_name) || in_array('add-season', $page_name)) { ?>
                <li class="treeview
                 {{ Request::is('demo/season-list') ? 'active' : '' }}
                 {{ Request::is('demo/add-season') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Seasons</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('season-list', $page_name))) { ?>
                            <li class="{{ Request::is('demo/season-list') ? 'subactive' : '' }}"><a href="{{ URL::to('demo/season-list') }}"><i class="fa fa-arrow-right"></i> Season List</a></li>
                        <?php
                        }
                        if ((in_array('add-season', $page_name))) { ?>
                            <li class="{{ Request::is('demo/add-season') ? 'subactive' : '' }}"><a href="{{ URL::to('demo/add-season') }}"><i class="fa fa-arrow-right"></i> Add Season</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }

            if (in_array('lead-list', $page_name) || in_array('open-lead-list', $page_name) || in_array('close-lead-list', $page_name)) { ?>
            <li class="treeview
                 {{ Request::is('admin/lead-list') ? 'active' : '' }}
                 {{ Request::is('admin/open-lead-list') ? 'active' : '' }}
                 {{ Request::is('admin/close-lead-list') ? 'active' : '' }}
                ">
                <a href="javascript:void(0)">
                    <i class="fa fa fa-bullhorn"></i>
                    <span>Lead</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                    if (in_array('lead-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('admin/lead-list') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('admin/lead-list') }}">
                                <i class="fa fa fa-bullhorn"></i>
                                <span>leads</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('open-lead-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('admin/open-lead-list') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('admin/open-lead-list') }}">
                                <i class="fa fa fa-bullhorn"></i>
                                <span>Open Lead List</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    if (in_array('close-lead-list', $page_name)) { ?>
                        <li class="treeview {{ Request::is('admin/close-lead-list') ? 'subactive' : '' }}">
                            <a href="{{ URL::to('admin/close-lead-list') }}">
                                <i class="fa fa fa-bullhorn"></i>
                                <span>Close Lead List</span>
                                <i class="fa pull-right"></i>
                            </a>
                        </li>                
                        <?php
                    }
                    ?>
                </ul>
            </li>

                <?php
            }
            
            if (in_array('message', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('demo/message') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('demo/message') }}">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <span>Message</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('activities', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('admin/activities') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('admin/activities') }}">
                        <i class="fa fa-history" aria-hidden="true"></i>
                        <span>Activities</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('reminder-list', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('admin/reminder-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('admin/reminder-list') }}">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                        <span>Reminders</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('devices', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('demo/devices') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('demo/devices') }}">
                        <i class="fa fa-mobile" aria-hidden="true"></i>
                        <span>Devices</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('get-village-list', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('admin/get-village-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('admin/get-village-list') }}">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        <span>Village List</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('organogram-list', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('admin/organogram-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('admin/organogram-list') }}">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <span>Organogram</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li> 
                <?php
            }
            if (in_array('data-source-list', $page_name)) { ?>
                 <li class="treeview
                 {{ Request::is('admin/data-source-list') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('admin/data-source-list') }}">
                        <i class="fa fa-info" aria-hidden="true"></i>
                        <span>Data Source</span>
                        <i class="fa pull-right"></i>
                    </a>
                </li>
                <?php
            }

            if (in_array('campaign', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('admin/campaign') ? 'active' : '' }}
                ">
                    <a href="{{ URL::to('admin/campaign') }}">
                        <i class="fa fa-users"></i>
                        <span>Campaign</span>
                    </a>
                </li>
            <?php
            }
            if (in_array('list-reports', $page_name) || in_array('add-reports', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('reports/add-reports') ? 'active' : '' }}
                    {{ Request::is('reports/list-reports') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Reports and Guidelines</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('add-reports', $page_name))) { ?>
                            <li class="{{ Request::is('reports/add-reports') ? 'subactive' : '' }}"><a href="{{ URL::to('reports/add-reports') }}"><i class="fa fa-arrow-right"></i> Add Reports</a></li>
                        <?php
                        }
                        if ((in_array('list-reports', $page_name))) { ?>
                            <li class="{{ Request::is('reports/list-reports') ? 'subactive' : '' }}"><a href="{{ URL::to('reports/list-reports') }}"><i class="fa fa-arrow-right"></i> View Reports</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }

            if (in_array('add-godown', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('godown/add-godown') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Godown</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('add-godown', $page_name))) { ?>
                            <li class="{{ Request::is('godown/add-godown') ? 'subactive' : '' }}"><a href="{{ URL::to('godown/add-godown') }}"><i class="fa fa-arrow-right"></i> Add Godown</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }

            if (in_array('list-request', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('demorequest/list-request') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Demo Material Request</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('list-request', $page_name))) { ?>
                            <li class="{{ Request::is('demorequest/list-request') ? 'subactive' : '' }}"><a href="{{ URL::to('demorequest/list-request') }}"><i class="fa fa-arrow-right"></i> List Material Request</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }

            if (in_array('list-teaestate', $page_name)  || in_array('list-atp', $page_name)  || in_array('list-arival-departure', $page_name) || in_array('list-target', $page_name) || in_array('list-sales-report', $page_name) || in_array('list-visitsummary', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('teaestate/list-teaestate') ? 'active' : '' }}
                    {{ Request::is('teaestate/list-atp') ? 'active' : '' }}
                    {{ Request::is('teaestate/list-arival-departure') ? 'active' : '' }}
                    {{ Request::is('teaestate/list-target') ? 'active' : '' }}
                    {{ Request::is('teaestate/list-sales-report') ? 'active' : '' }}
                    {{ Request::is('teaestate/list-visitsummary') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Tea Estate</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('list-teaestate', $page_name))) { ?>
                            <li class="{{ Request::is('teaestate/list-teaestate') ? 'subactive' : '' }}"><a href="{{ URL::to('teaestate/list-teaestate') }}"><i class="fa fa-arrow-right"></i> List Tea Estate</a></li>
                        <?php } if ((in_array('list-atp', $page_name))) { ?>
                            <li class="{{ Request::is('teaestate/list-atp') ? 'subactive' : '' }}"><a href="{{ URL::to('teaestate/list-atp') }}"><i class="fa fa-arrow-right"></i> List ATP</a></li>
                        <?php } if ((in_array('list-arival-departure', $page_name))) { ?>
                            <li class="{{ Request::is('teaestate/list-arival-departure') ? 'subactive' : '' }}"><a href="{{ URL::to('teaestate/list-arival-departure') }}"><i class="fa fa-arrow-right"></i> List Arrival Departure</a></li>
                        <?php } if ((in_array('list-target', $page_name))) { ?>
                            <li class="{{ Request::is('teaestate/list-target') ? 'subactive' : '' }}"><a href="{{ URL::to('teaestate/list-target') }}"><i class="fa fa-arrow-right"></i> List Target</a></li>
                        <?php } if ((in_array('list-sales-report', $page_name))) { ?>
                            <li class="{{ Request::is('teaestate/list-sales-report') ? 'subactive' : '' }}"><a href="{{ URL::to('teaestate/list-sales-report') }}"><i class="fa fa-arrow-right"></i> List Sales Report</a></li>
                        <?php } if ((in_array('list-visitsummary', $page_name))) { ?>
                            <li class="{{ Request::is('teaestate/list-visitsummary') ? 'subactive' : '' }}"><a href="{{ URL::to('teaestate/list-visitsummary') }}"><i class="fa fa-arrow-right"></i> List Visit Summary</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }

            if (in_array('list-influencer', $page_name)  || in_array('list-atp', $page_name)  || in_array('list-arival-departure', $page_name) || in_array('list-target', $page_name) || in_array('list-sales-report', $page_name) || in_array('list-visitsummary', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('influencer/list-influencer') ? 'active' : '' }}
                    {{ Request::is('influencer/list-atp') ? 'active' : '' }}
                    {{ Request::is('influencer/list-arival-departure') ? 'active' : '' }}
                    {{ Request::is('influencer/list-target') ? 'active' : '' }}
                    {{ Request::is('influencer/list-sales-report') ? 'active' : '' }}
                    {{ Request::is('influencer/list-visitsummary') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Influencer</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('list-influencer', $page_name))) { ?>
                            <li class="{{ Request::is('influencer/list-influencer') ? 'subactive' : '' }}"><a href="{{ URL::to('influencer/list-influencer') }}"><i class="fa fa-arrow-right"></i> List Influencer</a></li>
                        <?php } if ((in_array('list-atp', $page_name))) { ?>
                            <li class="{{ Request::is('influencer/list-atp') ? 'subactive' : '' }}"><a href="{{ URL::to('influencer/list-atp') }}"><i class="fa fa-arrow-right"></i> List ATP</a></li>
                        <?php } if ((in_array('list-arival-departure', $page_name))) { ?>
                            <li class="{{ Request::is('influencer/list-arival-departure') ? 'subactive' : '' }}"><a href="{{ URL::to('influencer/list-arival-departure') }}"><i class="fa fa-arrow-right"></i> List Arrival Departure</a></li>
                        <?php } if ((in_array('list-target', $page_name))) { ?>
                            <li class="{{ Request::is('influencer/list-target') ? 'subactive' : '' }}"><a href="{{ URL::to('influencer/list-target') }}"><i class="fa fa-arrow-right"></i> List Target</a></li>
                        <?php } if ((in_array('list-sales-report', $page_name))) { ?>
                            <li class="{{ Request::is('influencer/list-sales-report') ? 'subactive' : '' }}"><a href="{{ URL::to('influencer/list-sales-report') }}"><i class="fa fa-arrow-right"></i> List Sales Report</a></li>
                        <?php } if ((in_array('list-visitsummary', $page_name))) { ?>
                            <li class="{{ Request::is('influencer/list-visitsummary') ? 'subactive' : '' }}"><a href="{{ URL::to('influencer/list-visitsummary') }}"><i class="fa fa-arrow-right"></i> List Visit Summary</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }
            if (in_array('list-sugarmill', $page_name)  || in_array('list-atp', $page_name)  || in_array('list-arival-departure', $page_name) || in_array('list-target', $page_name) || in_array('list-sales-report', $page_name) || in_array('list-visitsummary', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('sugarmill/list-sugarmill') ? 'active' : '' }}
                    {{ Request::is('sugarmill/list-atp') ? 'active' : '' }}
                    {{ Request::is('sugarmill/list-arival-departure') ? 'active' : '' }}
                    {{ Request::is('sugarmill/list-target') ? 'active' : '' }}
                    {{ Request::is('sugarmill/list-sales-report') ? 'active' : '' }}
                    {{ Request::is('sugarmill/list-visitsummary') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Sugarmill</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('list-sugarmill', $page_name))) { ?>
                            <li class="{{ Request::is('sugarmill/list-sugarmill') ? 'subactive' : '' }}"><a href="{{ URL::to('sugarmill/list-sugarmill') }}"><i class="fa fa-arrow-right"></i> List Sugarmill</a></li>
                        <?php } if ((in_array('list-atp', $page_name))) { ?>
                            <li class="{{ Request::is('sugarmill/list-atp') ? 'subactive' : '' }}"><a href="{{ URL::to('sugarmill/list-atp') }}"><i class="fa fa-arrow-right"></i> List ATP</a></li>
                        <?php } if ((in_array('list-arival-departure', $page_name))) { ?>
                            <li class="{{ Request::is('sugarmill/list-arival-departure') ? 'subactive' : '' }}"><a href="{{ URL::to('sugarmill/list-arival-departure') }}"><i class="fa fa-arrow-right"></i> List Arrival Departure</a></li>
                        <?php } if ((in_array('list-target', $page_name))) { ?>
                            <li class="{{ Request::is('sugarmill/list-target') ? 'subactive' : '' }}"><a href="{{ URL::to('sugarmill/list-target') }}"><i class="fa fa-arrow-right"></i> List Target</a></li>
                        <?php } if ((in_array('list-sales-report', $page_name))) { ?>
                            <li class="{{ Request::is('sugarmill/list-sales-report') ? 'subactive' : '' }}"><a href="{{ URL::to('sugarmill/list-sales-report') }}"><i class="fa fa-arrow-right"></i> List Sales Report</a></li>
                        <?php } if ((in_array('list-visitsummary', $page_name))) { ?>
                            <li class="{{ Request::is('sugarmill/list-visitsummary') ? 'subactive' : '' }}"><a href="{{ URL::to('sugarmill/list-visitsummary') }}"><i class="fa fa-arrow-right"></i> List Visit Summary</a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php
            }

            if (in_array('list-testimonials', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('institutional/list-testimonials') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Instituonal </span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('list-testimonials', $page_name))) { ?>
                            <li class="{{ Request::is('institutional/list-testimonials') ? 'subactive' : '' }}"><a href="{{ URL::to('institutional/list-testimonials') }}"><i class="fa fa-arrow-right"></i> List Testimonial</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }
            if (in_array('list-distributor-advisor', $page_name)  || in_array('list-atp', $page_name)) { ?>
                <li class="treeview
                    {{ Request::is('distributoradvisor/list-distributor-advisor') ? 'active' : '' }}
                    {{ Request::is('distributoradvisor/list-atp') ? 'active' : '' }}
                ">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Distributor Advisor</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ((in_array('list-distributor-advisor', $page_name))) { ?>
                            <li class="{{ Request::is('distributoradvisor/list-distributor-advisor') ? 'subactive' : '' }}">
                                <a href="{{ URL::to('distributoradvisor/list-distributor-advisor') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    List Distributor Advisor
                                </a>
                            </li>
                        <?php } if ((in_array('list-atp', $page_name))) { ?>
                            <li class="{{ Request::is('distributoradvisor/list-atp') ? 'subactive' : '' }}">
                                <a href="{{ URL::to('distributoradvisor/list-atp') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    List ATP
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } if (in_array('list-circular', $page_name)) { ?>
                <li class="treeview {{ Request::is('circulars/list-circular') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-clipboard"></i>
                        <span>Weekly Circular</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (in_array('list-circular', $page_name)) { ?>
                            <li class="{{ Request::is('circulars/list-circular') ? 'subactive' : '' }}">
                                <a href="{{ URL::to('circulars/list-circular') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    List Circulars
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
