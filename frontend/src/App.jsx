import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Suspense, lazy } from 'react';
import { useAnalytics } from './hooks/useAnalytics';
import Layout from './components/Layout';
import ProtectedRoute from './components/ProtectedRoute';
import AdminLayout from './components/AdminLayout';
import ScrollToTop from './components/ScrollToTop';
// Home is eagerly imported: it is the LCP-critical landing page and the
// dynamic-import waterfall was costing ~300-500ms of mobile LCP.
import Home from './pages/Home';

// Public pages — lazy loaded for code splitting
const Services = lazy(() => import('./pages/Services'));
const ServiceDetails = lazy(() => import('./pages/ServiceDetails'));
const About = lazy(() => import('./pages/About'));
const Contact = lazy(() => import('./pages/Contact'));
const Login = lazy(() => import('./pages/Login'));
const Register = lazy(() => import('./pages/Register'));
const Dashboard = lazy(() => import('./pages/Dashboard'));
const AiSupport = lazy(() => import('./pages/AiSupport'));
const Blog = lazy(() => import('./pages/Blog'));
const BlogPost = lazy(() => import('./pages/BlogPost'));
const PrivacyPolicy = lazy(() => import('./pages/PrivacyPolicy'));
const Terms = lazy(() => import('./pages/Terms'));

// Admin pages — separate chunk, only loaded when needed
const AdminDashboard = lazy(() => import('./pages/admin/AdminDashboard'));
const ManageServices = lazy(() => import('./pages/admin/ManageServices'));
const ManageMessages = lazy(() => import('./pages/admin/ManageMessages'));
const ManageKnowledgeBase = lazy(() => import('./pages/admin/ManageKnowledgeBase'));
const AdminAnalytics = lazy(() => import('./pages/admin/AdminAnalytics'));
const ManageAiChats = lazy(() => import('./pages/admin/ManageAiChats'));
const ManageBlogPosts = lazy(() => import('./pages/admin/ManageBlogPosts'));

const PageLoader = () => (
  <div className="flex items-center justify-center min-h-[60vh]">
    <div className="w-8 h-8 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin" />
  </div>
);

const AnalyticsTracker = () => {
  useAnalytics();
  return null;
};

function App() {
  return (
    <Router>
      <ScrollToTop />
      <AnalyticsTracker />
      <Suspense fallback={<PageLoader />}>
        <Routes>
          {/* Public Routes */}
          <Route path="/login" element={<Login />} />

          {/* Public Routes with Layout */}
          <Route path="/" element={<Layout />}>
            <Route index element={<Home />} />
            <Route path="services" element={<Services />} />
            <Route path="services/:slug" element={<ServiceDetails />} />
            <Route path="about" element={<About />} />
            <Route path="contact" element={<Contact />} />
            <Route path="ai-support" element={<AiSupport />} />
            <Route path="blog" element={<Blog />} />
            <Route path="blog/:slug" element={<BlogPost />} />
            <Route path="privacy" element={<PrivacyPolicy />} />
            <Route path="terms" element={<Terms />} />

            {/* Protected User Routes */}
            <Route element={<ProtectedRoute />}>
              <Route path="dashboard" element={<Dashboard />} />
            </Route>
          </Route>

          {/* Protected Admin Routes */}
          <Route element={<ProtectedRoute allowedRoles={['admin']} />}>
            <Route path="/admin" element={<AdminLayout />}>
              <Route path="dashboard" element={<AdminDashboard />} />
              <Route path="analytics" element={<AdminAnalytics />} />
              <Route path="services" element={<ManageServices />} />
              <Route path="messages" element={<ManageMessages />} />
              <Route path="knowledge-base" element={<ManageKnowledgeBase />} />
              <Route path="ai-chats" element={<ManageAiChats />} />
              <Route path="blog-posts" element={<ManageBlogPosts />} />
            </Route>
          </Route>
        </Routes>
      </Suspense>
    </Router>
  );
}

export default App;
